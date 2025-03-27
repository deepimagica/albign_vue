<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        // dd($request);
        $user = Auth::guard('user')->user();
        $userId = $user->id;
        $employeePos = $user->employee_pos;
        $surveyType = $request->get('dashboard', 0);
        $search = $request->get('search', '');
        $selectedSurveyId = $request->get('survey_id', '');
        $query = DB::table('doctors')
            ->join('survey', 'doctors.survey_id', '=', 'survey.survey_id')
            ->join('users as users1', 'doctors.user_id', '=', 'users1.id')
            ->leftJoin('users as users2', 'users1.parent_employee_code', '=', 'users2.employee_code')
            ->leftJoin('users as users3', 'users2.parent_employee_code', '=', 'users3.parent_employee_code')
            ->leftJoin('users as users4', 'users3.parent_employee_code', '=', 'users4.parent_employee_code')
            ->select('doctors.*', 'survey.title as survey_title', 'survey.type as survey_type', 'users1.employee_pos', 'survey.survey_id')
            ->where('doctors.is_deleted', 0)
            ->where(function ($query) use ($userId, $employeePos) {
                if ($employeePos == 3) {
                    $query->where('users4.id', $userId);
                } elseif ($employeePos == 2) {
                    $query->where('users3.id', $userId);
                } elseif ($employeePos == 1) {
                    $query->where('users2.id', $userId);
                } else {
                    $query->where('doctors.user_id', $userId);
                }
            })
            ->where(function ($query) {
                $query->where('survey.status', 2)
                    ->orWhere('doctors.is_test', 1);
            })
            ->where('survey.type', 0)
            ->where('survey.is_survey', $surveyType);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('doctors.name', 'like', "%$search%")
                    ->orWhere('survey.title', 'like', "%$search%");
            });
        }

        if (!empty($selectedSurveyId)) {
            $query->where('survey.survey_id', $selectedSurveyId);
        }

        $query->orderBy('doctors.is_accept', 'asc')
            ->orderBy('doctors.updated_date', 'desc');

        $doctors = $query->get();

        foreach ($doctors as $doctor) {
            $doctor->encrypted_id = Crypt::encrypt($doctor->id);
        }

        $surveyList = DB::table('survey')
            ->where('sector_id', $user->sector_id)
            ->where('status', 2)
            ->where('type', 0)
            ->where('is_survey', $surveyType)
            ->where('is_del', 0)
            ->orderBy('survey_id', 'asc')
            ->get();

        $uniqueNumber = $this->get_unique_number('doctors', 'send_code', 5, false);
        return Inertia::render('User/Dashboard', [
            'user' => $user,
            'surveyType' => $surveyType,
            'doctors' => $doctors,
            'surveyList' => $surveyList,
            'uniqueNumber' => $uniqueNumber,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function getAgreementPage(Request $request, $doctor_id)
    {
        try {
            $user = Auth::guard('user')->user();
            $decryptedDoctorId = Crypt::decrypt($doctor_id);
            $response = [];
            $response['csrf'] = [
                'name' => csrf_token(),
                'hash' => csrf_token(),
            ];

            $doctorId = is_numeric($decryptedDoctorId) ? $decryptedDoctorId : 0;

            $is_check = $request->query('is_check', 0);
            $is_check = is_numeric($is_check) ? $is_check : 0;

            $doctor = DB::table('doctors')->where('id', $doctorId)->where('is_deleted', 0)->first();

            if ($doctor) {
                if ($doctor->is_agreement_verified && !$is_check) {
                    return redirect()->route('dashboard');
                } else {
                    $response['doctor'] = $doctor;
                    $survey_id = $doctor->survey_id;

                    $survey = DB::table('survey')->where('survey_id', $survey_id)->first();
                    if (!$survey) {
                        return redirect()->route('dashboard');
                    }

                    $response['survey'] = $survey;
                    $sector_id = $survey->sector_id;

                    $sector = DB::table('sectors')->where('sector_id', $sector_id)->first();

                    if ($survey->agreement_type == 1 && now()->gt(Carbon::parse($survey->agreement_date)->addDays(364))) {
                        $isExpired = $this->checkDoctorExpiration($doctor, $sector_id);
                        $response['isExpired'] = $isExpired;
                    }

                    if ($survey->agreement_type == 4 && now()->gt(Carbon::parse($doctor->agreement_date)->addDays(364))) {
                        $isExpired = $this->checkDoctorExpiration($doctor, $sector_id);
                        $response['isExpired'] = $isExpired;
                    }

                    $convertedAmount = $this->getIndianCurrency($doctor->honorarium);
                    $response['convertedAmount'] = $convertedAmount;
                    $response['doc_enc_id'] = $doctor_id;

                    return Inertia::render('User/Agreement', [
                        'response' => $response,
                        'user' => $user,
                    ]);
                }
            } else {
                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
    }


    private function checkDoctorExpiration($doctor, $sector_id)
    {
        $existingDoctor = DB::table('doctors')
            ->join('survey', 'doctors.survey_id', '=', 'survey.survey_id')
            ->where('doctors.id', '!=', $doctor->id)
            ->where('doctors.doctor_uid', $doctor->doctor_uid)
            ->where('doctors.doctor_type', $doctor->doctor_type)
            ->where('survey.sector_id', $sector_id)
            ->where('doctors.otp_verified', 1)
            ->where('survey.is_survey', 1)
            ->where('doctors.is_deleted', 0)
            ->limit(1)
            ->first();

        return $existingDoctor ? 1 : 0;
    }

    public function storeAgreementData(Request $request, $doctor_id)
    {
        $data = $this->decryptData($request->data);
        $decryptedDoctorId = $data['doctor_id'];
        $validator = Validator::make($data, [
            'is_agree' => 'required',
        ], [
            'is_agree.required' => 'Please click on the accept button.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        if (isset($decryptedDoctorId) && is_numeric($decryptedDoctorId)) {
            if ($data['is_agree']) {
                $insertedFields = [
                    'is_agreement_verified' => 1,
                    'updated_date' => now(),
                    'browser' => $this->getIPDetail()
                ];

                $updated = DB::table('doctors')
                    ->where('id', $decryptedDoctorId)
                    ->update($insertedFields);
                if (isset($updated['statuscode']) && $updated['statuscode'] == 1) {
                    return redirect()->route('agreement', ['doctor_id' => $doctor_id]);
                }
                return redirect()->route('get.survey', ['doctor_id' => $doctor_id]);
            } else {
                return redirect()->route('dashboard');
            }
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function getSurveyPage(Request $request, $doctor_id)
    {
        $doc_id = $doctor_id;
        $user = Auth::guard('user')->user();
        $decryptedDoctorId = Crypt::decrypt($doctor_id);
        // $decryptedDoctorId = $doctor_id;
        $doctor = DB::table('doctors')->where('id', $decryptedDoctorId)->where('is_deleted', 0)->first();

        if (!$doctor) {
            return redirect()->route('dashboard');
        }

        $survey_id = $doctor->survey_id;
        $totalQuestions = DB::table('questions')
            ->where('is_active', 1)
            ->where('survey_id', $survey_id)
            ->count();

        $completedQuestionIds = DB::table('answers')->where('doctor_id', $decryptedDoctorId)->where('is_next', 1)->pluck('question_id')->toArray();

        $remainingQuestions = max(0, $totalQuestions - count($completedQuestionIds));

        $isLast = intval($remainingQuestions == 1);

        $nextQuestion = DB::table('questions')
            ->where('is_active', 1)
            ->where('survey_id', $survey_id)
            // ->whereNotIn('id', $completedQuestionIds)
            ->orderBy('id', 'asc')
            ->first();

        $encryptedQuestionId = $nextQuestion ? Crypt::encrypt($nextQuestion->id) : null;

        if ($nextQuestion && $nextQuestion->depend_id != 0) {
            $dependAnswer = DB::table('answers')
                ->where('doctor_id', $decryptedDoctorId)
                ->where('question_id', $nextQuestion->depend_id)
                ->where('is_next', 1)
                ->first();

            if ($dependAnswer && strpos($dependAnswer->answers, $nextQuestion->depend_answers) === false) {
                DB::table('answers')->updateOrInsert(
                    [
                        'doctor_id' => $decryptedDoctorId,
                        'question_id' => $nextQuestion->id,
                    ],
                    [
                        'answers' => '',
                        'is_skiped' => 1,
                        'is_next' => 1,
                        'updated_date' => now(),
                    ]
                );
                return redirect()->route('get.survey', ['doctor_id' => $decryptedDoctorId]);
            }
        }

        $currentAnswer = DB::table('answers')
            ->where('doctor_id', $decryptedDoctorId)
            ->where('question_id', optional($nextQuestion)->id)
            ->first();
        $response = [
            'doctor' => $doctor,
            'nextQuestion' => $nextQuestion,
            'currentAnswer' => $currentAnswer,
            'isLast' => $isLast,
            'decryptedDoctorId' => $decryptedDoctorId,
            'doc_id' => $doc_id,
            'user' => $user,
            'encryptedQuestionId' => $encryptedQuestionId,
        ];
        return Inertia::render('User/Survey', [
            'response' => $response
        ]);
    }


    public function storeAnswer(Request $request)
    {
        try {
            $decodedData = $this->decryptData($request->encryptedData);
            // $decodedData = [];
            // parse_str(utf8_decode(base64_decode($request->encryptedData)), $decodedData);
            // \Log::info('Decoded Survey Data:', $decodedData);
            $decryptedDoctorId = $decodedData['doc_id'];
            $rawQuestion = $decodedData['question'];

            try {
                $decrypted = is_numeric($rawQuestion) ? $rawQuestion : Crypt::decryptString($rawQuestion);
                preg_match('/\d+/', $decrypted, $matches);
                $question_id = isset($matches[0]) ? (int) $matches[0] : null;
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                \Log::info('Decryption failed: ' . $e->getMessage());
            }

            $answer = $decodedData['answer'];
            $isLast = $decodedData['is_last'];
            $doctor = DB::table('doctors')->where('id', $decryptedDoctorId)->where('is_deleted', 0)->first();
            $survey_id = $doctor->survey_id;

            $data = DB::table('answers')->updateOrInsert(
                [
                    'doctor_id' =>  $decryptedDoctorId,
                    'question_id' => $question_id,
                ],
                [
                    'answers' => $answer,
                    'is_next' => 1,
                    'updated_date' => now(),
                ]
            );

            $remainingQuestionsCount = DB::table('questions')
                ->where('survey_id', $survey_id)
                ->where('id', '>', $question_id)
                ->count();

            if ($isLast) {
                return response()->json(['success' => true, 'isLast' => true, 'redirect_url' => route('accountDetail', $decodedData['doctor_id'])]);
            }

            $nextQuestion = DB::table('questions')
                ->where('survey_id', $survey_id)
                ->where('id', '>', $question_id)
                ->orderBy('id', 'asc')
                ->first();
            $currentAnswer = DB::table('answers')
                ->where('doctor_id', $decryptedDoctorId)
                ->where('question_id', optional($nextQuestion)->id)
                ->first();

            if ($nextQuestion && $nextQuestion->id != $question_id) {
                $dataToEncode = [
                    'nextQuestion' => $nextQuestion,
                    'currentAnswer' => $currentAnswer->answers ?? '',
                    'remainingQuestionsCount' => $remainingQuestionsCount,
                    'isLast' => $isLast,
                ];

                $encodedData = base64_encode(json_encode($dataToEncode));

                return response()->json([
                    'success' => true,
                    'data' => $encodedData
                ]);
            }
            return response()->json(['redirect' => true, 'redirect_url' => route('confirmation', $decodedData['doctor_id'])]);
            // return redirect()->route('confirmation',$decodedData['doctor_id']);
            // return redirect()->route('confirmation',['doctor_id' => $decodedData['doctor_id']]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            dd($e);
            return response()->json(['error' => 'Invalid payload', 'message' => $e->getMessage()]);
        }
    }


    public function getPreviousAnswer(Request $request)
    {
        $currentQuestionId = $request->current_question_id;
        $doctorId = $request->doctor_id;
        // if (is_numeric($currentQuestionId)) {
        //     $question_id = $request->current_question_id;
        // } else {
        //     $decrypted = Crypt::decryptString($currentQuestionId);
        //     preg_match('/\d+/', $decrypted, $matches);
        //     $question_id = isset($matches[0]) ? (int) $matches[0] : null;
        // }
        $decrypted = is_numeric($currentQuestionId) ? $currentQuestionId : Crypt::decryptString($currentQuestionId);
        preg_match('/\d+/', $decrypted, $matches);
        $question_id = isset($matches[0]) ? (int) $matches[0] : null;
        $doctor = DB::table('doctors')->where('id', $doctorId)->where('is_deleted', 0)->first();
        $survey_id = $doctor->survey_id;
        if (!$doctorId) {
            return response()->json(['success' => false, 'message' => 'Doctor ID is required.'], 400);
        }

        $previousQuestion = DB::table('questions')
            ->where('id', '=', $question_id)
            ->where('survey_id', $survey_id)
            ->orderBy('id', 'desc')
            ->first();


        if (!$previousQuestion) {
            return response()->json(['success' => false, 'message' => 'No previous question found.']);
        }

        $previousAnswer = DB::table('answers')
            ->where('doctor_id', $doctorId)
            ->where('question_id', $previousQuestion->id)
            ->value('answers');

        return response()->json([
            'success' => true,
            'question' => $previousQuestion,
            'answer' => $previousAnswer ?? ''
        ]);
    }

    public function getConfirmationPage(Request $request, $doctor_id)
    {
        try {
            $user = Auth::guard('user')->user();
            if ($user) {
                $data = [];
                $decodedDoctorId =  $doctor_id;

                if (base64_encode(base64_decode($doctor_id, true)) === $doctor_id) {
                    $decryptedDoctorId = Crypt::decryptString($doctor_id);
                    $doctor_id = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;
                } else {
                    $doctor_id = base64_decode($doctor_id);
                    $decryptedDoctorId = Crypt::decryptString($doctor_id);
                    $doctor_id = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;
                }
                if (is_numeric($doctor_id)) {
                    $doctor = DB::table('doctors as d')
                        ->leftJoin('main_doctors as md', 'md.uin', '=', 'd.doctor_uid')
                        ->select('d.*', 'md.qualification as qu', 'md.experience as exp', 'md.mci_registration as mci')
                        ->where('d.id', $doctor_id)
                        ->where('d.is_deleted', 0)
                        ->first();

                    if ($doctor) {
                        $uin = $doctor->uin;
                        $d_id = $doctor->id;
                        $survey_id = $doctor->survey_id;

                        $doctorDetails = DB::table('doctors as d')
                            ->leftJoin('main_doctors as md', 'md.uin', '=', 'd.doctor_uid')
                            ->select('d.*', 'md.qualification as qu', 'md.experience as exp', 'md.mci_registration as mci')
                            ->where(function ($query) use ($uin, $d_id) {
                                $query->where('d.uin', $uin)
                                    ->orWhere('d.id', $d_id);
                            })
                            ->where(function ($query) {
                                $query->whereNotNull('d.pan_number')
                                    ->orWhere('d.pan_number', '');
                            })
                            ->where(function ($query) {
                                $query->whereNotNull('d.registration_no')
                                    ->orWhereNull('d.registration_no');
                            })
                            ->where('d.is_deleted', 0)
                            ->first();

                        if ($doctorDetails) {
                            $survey = DB::table('survey')
                                ->where('survey_id', $survey_id)
                                ->first();
                            if ($survey) {
                                $data = [
                                    'csrf' => [
                                        'name' => csrf_token(),
                                        'hash' => csrf_token(),
                                    ],
                                    'logged_in' => $user,
                                    'doctor' => $doctor,
                                    'doctorDetails' => $doctorDetails,
                                    'survey' => $survey,
                                ];
                                return Inertia::render('User/DoctorConfirm', [
                                    'data'  => $data,
                                    'user'  => $user,
                                    'decodedDoctorId'  => $decodedDoctorId
                                ]);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('admin');
        }
    }

    public function storeConfirmationData(Request $request, $doctor_id)
    {
        try {
            // $decodedData = $this->decryptData($request->data);
            $decodedData = $request->data;
            $user = Auth::guard('user')->user();
            $decryptedDoctorId = $decodedData['doctor_id'];
            $doctor = DB::table('doctors')
                ->where('id', $decryptedDoctorId)
                ->where('is_deleted', 0)
                ->first();

            $validator = Validator::make($decodedData, [
                'name' => 'required|regex:/^[a-zA-Z\s\-\'\.]+$/u|max:80',
                'mobile' => [
                    'required',
                    'numeric', // Ensures the value is a number
                    'digits:10', // Ensures exactly 10 digits
                    Rule::unique('doctors')->ignore($decryptedDoctorId),
                ],

                'address' => 'required|regex:/^[a-zA-Z0-9\s,.\'\/\-\()\[\]]+$/|max:500',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('doctors')->ignore($decryptedDoctorId),
                ],
                'pin_code' => 'required|regex:/^[0-9]{6}$/',
                'registration_no' => 'required|max:80',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors());
            }
            if ($doctor && isset($decodedData['mobile'], $decodedData['name'])) {
                $updateData = [
                    'is_confirm' => 1,
                    'is_agree' => $decodedData['agree'] ? 1 : 0,
                    'pin_code' => trim($decodedData['pin_code']),
                    'name' => trim($decodedData['name']),
                    'mobile' => trim($decodedData['mobile']),
                    'email' => trim($decodedData['email']),
                    'registration_no' => trim($decodedData['registration_no']),
                    'registration_state' => trim($decodedData['address']),
                    'address' => trim($decodedData['address']),
                    'updated_date' => now(),
                ];

                $data = DB::table('doctors')
                    ->where('id', $decryptedDoctorId)
                    ->update($updateData);

                // return redirect()->route('accountDetail', ['doctor_id' => $decryptedDoctorId]);
                return redirect()->route('accountDetail', ['doctor_id' => $request->doctor_id]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors(),], 422);
        } catch (\Exception $e) {
            // dd($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong.', 'redirect_url' => route('confirmation', ['doctor_id' => $doctor_id]), 'error' => $e->getMessage(),], 500);
        }
    }

    public function getAccountDetailPage(Request $request, $doctor_id)
    {
        try {
            $decodedDocId = $doctor_id;
            if (base64_encode(base64_decode($doctor_id, true)) === $doctor_id) {
                $decryptedDoctorId = Crypt::decryptString($doctor_id);
                $doctor_id = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;
            } else {
                \Log::info('The provided string is not a valid Base64 encoded string');
            }
            $user = Auth::guard('user')->user();
            $data = [];
            $doc_id = $doctor_id;
            $doctor = DB::table('doctors as d')
                ->leftJoin('main_doctors as md', 'md.uin', '=', 'd.doctor_uid')
                ->select(
                    'd.*',
                    'md.qualification as qu',
                    'md.experience as exp',
                    'md.mci_registration as mci'
                )
                ->where('d.id', $doctor_id)
                ->where('d.is_deleted', 0)
                ->first();

            $doctorDetails = DB::table('doctors as d')
                ->leftJoin('main_doctors as md', 'md.uin', '=', 'd.doctor_uid')
                ->select(
                    'd.*',
                    'md.qualification as qu',
                    'md.experience as exp',
                    'md.mci_registration as mci'
                )
                ->where(function ($query) use ($doctor) {
                    $query->where('d.uin', $doctor->uin)
                        ->orWhere('d.id', $doctor->id);
                })
                ->where(function ($query) {
                    $query->whereNotNull('d.pan_number')
                        ->orWhere('d.pan_number', '');
                })
                ->where(function ($query) {
                    $query->whereNotNull('d.registration_no')
                        ->orWhereNull('d.registration_no');
                })
                ->where('d.is_deleted', 0)
                ->first();

            $data['doctor'] = (array) $doctorDetails;
            $data['doctor']['d_id'] = $doctor->id;
            $data['logged_in'] = $user;

            $survey = DB::table('survey')
                ->where('survey_id', $doctor->survey_id)
                ->first();

            if ($survey) {
                $data['survey'] = (array) $survey;
                return Inertia::render('User/AccountDetails', [
                    'data' => $data,
                    'user' => $user,
                    'doc_id' => $doc_id,
                    'decodedDoctorId' => $decodedDocId
                ]);
            }
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Something went wrong!');
        }
    }

    public function storeAccountDetails(Request $request, $doctor_id)
    {
        try {
            $encodedDocId = $doctor_id;
            $encodedData = $this->decryptData($request->data);
            if (base64_encode(base64_decode($doctor_id, true)) === $doctor_id) {
                $decryptedDoctorId = Crypt::decryptString($doctor_id);
                $doctor_id = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;
            } else {
                \Log::info('The provided string is not a valid Base64 encoded string');
            }
            $user = Auth::guard('user')->user();

            $doctor = DB::table('doctors')
                ->where('id', $doctor_id)
                ->where('is_deleted', 0)
                ->first();

            if (!$doctor) {
                return response()->json(['success' => false, 'message' => 'Doctor not found.'], 404);
            }

            $validator = Validator::make($encodedData, [
                'IFSC_code' => 'required|string|size:11',
                'pan_number' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                'account_number' => 'required|string|max:25|regex:/^[0-9]+$/|unique:doctors,account_number',
                'cancel_cheque' => ['required', 'regex:/^data:image\/(png|jpg|jpeg|gif);base64,/'],
            ], [
                'IFSC_code.required' => 'The IFSC code is required.',
                'IFSC_code.size' => 'The IFSC code must be exactly 11 characters long.',
                'pan_number.required' => 'The PAN number is required.',
                'pan_number.regex' => 'Invalid PAN format.',
                'account_number.required' => 'Account number is required.',
                'account_number.regex' => 'Account number must be numeric.',
                'account_number.unique' => 'This account number already exists.',
                'cancel_cheque.required' => 'Please upload a cancel cheque image.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors());
            }

            $existingDoctor = DB::table('doctors')
                ->join('survey', 'survey.survey_id', '=', 'doctors.survey_id')
                ->where('doctors.is_test', 0)
                ->where('survey.is_survey', 0)
                ->where('doctors.survey_id', $doctor->survey_id)
                ->where('doctors.pan_number', 'like', '%' . strtolower($encodedData['pan_number']) . '%')
                ->where('doctors.id', '!=', $doctor_id)
                ->where('doctors.is_deleted', 0)
                ->exists();

            if ($existingDoctor) {
                return response()->json(['success' => false, 'message' => 'The PAN number has already been enrolled in the same survey.'], 409);
            }

            $insertedFields = [];
            $previousImagePath = $doctor->cancel_cheque ?? null;
            if ($encodedData['cancel_cheque']) {
                $croppedImageData = $encodedData['cancel_cheque'];
                [$imageType, $imageData] = explode(',', $croppedImageData);
                $imageExtension = str_replace('data:image/', '', explode(';', $imageType)[0]);
                $fileName = time() . '.' . $imageExtension;
                $destinationPath = public_path('assets/img/doctor/document');

                if (!is_dir($destinationPath)) mkdir($destinationPath, 0775, true);

                if ($previousImagePath && file_exists($destinationPath . '/' . $previousImagePath)) {
                    unlink($destinationPath . '/' . $previousImagePath);
                }

                file_put_contents($destinationPath . '/' . $fileName, base64_decode($imageData));
                $insertedFields['cancel_cheque'] = $fileName;
            }


            $insertedFields['pan_number'] = trim($encodedData['pan_number']);
            $insertedFields['account_number'] = trim($encodedData['account_number']);
            $insertedFields['IFSC_code'] = trim($encodedData['IFSC_code']);
            $insertedFields['updated_date'] = now();


            DB::table('doctors')->where('id', $doctor_id)->update($insertedFields);

            if ($doctor && $doctor->otp == 0) {
                $otp = $this->getOTP(4, false);
                if (isset($request->mobile) && $request->mobile != '' && $otp != '') {
                    $mobile = $request->mobile;
                    dd($mobile);
                    $name = str_replace(' ', '+', $request->name);

                    if (strlen($name) > 25) {
                        $n = explode(" ", $name);
                        if (trim($n[0]) == 'Dr.' || trim($n[0]) == 'Dr') {
                            $n[0] = isset($n[0]) ? trim($n[0]) : '';
                            $n[1] = isset($n[1]) ? trim($n[1]) : '';
                            $name = $n[0] . '+' . $n[1] . '+' . substr(trim($n[2]), 0, 1) . '.';
                        } else {
                            $n[0] = isset($n[0]) ? trim($n[0]) : '';
                            $n[1] = isset($n[1]) ? trim($n[1]) : '';
                            $name = $n[0] . '+' . substr(trim($n[1]), 0, 1) . '.';
                        }
                    } else {
                        $name = str_replace(' ', '+', $name);
                    }

                    $platform = DB::table('platformChange')->where('id', 1)->first();
                    if ($platform && $platform->status == 0) {
                        $api = "https://m1.sarv.com/api/v2.0/sms_campaign.php?token=your_token&user_id=your_user_id&route=TR&template_id=12836&sender_id=imagic&language=EN&template=Dear+Doctor%0D%0A%0D%0AKindly+enter+below+OTP+to+submit+your+responses+for+the+Perception+Survey+study.%0D%0A%0D%0A" . $otp . "%0D%0A%0D%0ARgds%2C%0D%0AImagicahealth&contact_numbers=" . $mobile . "";
                        $response = file_get_contents($api);
                    } else {
                        $toNumber = '91' . $mobile;
                        $response = $this->sendWhatsAppMessage($toNumber, $otp);
                    }

                    $insertedFields['otp'] = $otp;
                    $insertedFields['otp_date'] = now();

                    $toEmail = $request->email;
                    $subject = "Alsign : OTP for Completion of Agreement Process";

                    $emailResponse = Http::withHeaders([
                        'Authorization' => 'Zoho-enczapikey your_api_key',
                    ])->post("https://api.zeptomail.in/v1.1/email/template", [
                        'mail_template_key' => 'your_mail_template_key',
                        'from' => [
                            'address' => 'vanita@vinciohealth.in',
                            'name' => 'Alsign',
                        ],
                        'to' => [
                            [
                                'email_address' => [
                                    'address' => $toEmail,
                                    'name' => $name,
                                ],
                            ],
                        ],
                        'merge_info' => [
                            'subject' => $subject,
                            'name' => $name,
                            'OTP' => $otp,
                            'team' => 'Alembic',
                            'product_name' => 'Alsign',
                        ],
                    ]);
                }
            }

            DB::table('doctors')->where('id', $doctor_id)->update($insertedFields);
            // return response()->json(['success' => true, 'redirect_url' => route('signature.page', ['doctor_id' => $doctor_id]), 'message' => 'Account details updated successfully.']);
            return redirect()->route('signature.page', ['doctor_id' => $encodedDocId]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors(),], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getSignaturePage(Request $request, $doctor_id)
    {
        $user = Auth::guard('user')->user();
        $doc_id =  $doctor_id;
        $decryptedDoctorId = Crypt::decryptString($doctor_id);
        $decryptedDoctorId = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;

        $doctor = DB::table('doctors')->where('id', $decryptedDoctorId)->where('is_deleted', 0)->first();

        if ($doctor->otp_verified) {
            return redirect()->route('dashboard');
        }

        $survey_id = $doctor->survey_id;
        $completedQuestionIds = DB::table('answers')->where('doctor_id', $decryptedDoctorId)->pluck('question_id')->toArray();

        $questionsCount = DB::table('questions')->where('is_active', 1)->where('survey_id', $survey_id)->count();

        $isLast = ($questionsCount - count($completedQuestionIds)) == 1;

        $nextQuestion = DB::table('questions')->where('is_active', 1)->where('survey_id', $survey_id)->whereNotIn('id', $completedQuestionIds)
            ->orderBy('id', 'asc')
            ->first();

        if ($nextQuestion) {
            return view('user.survey', [
                'doctor' => $doctor,
                'is_last' => $isLast,
                'question' => $nextQuestion,
                'user' => $user
            ]);
        } else {
        }
        return Inertia::render('User/Signature', [
            'doctor' => $doctor,
            'user' => $user,
            'doctor_id' => $doc_id
        ]);
    }

    public function verifySignature(Request $request, $doctor_id)
    {
        try {
            $user = Auth::guard('user')->user();
            $doc_id =  $doctor_id;
            $decryptedDoctorId = Crypt::decryptString($doctor_id);
            $decryptedDoctorId = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;

            $doctor = DB::table('doctors')->where('id', $decryptedDoctorId)->where('is_deleted', 0)->first();

            if ($doctor && !empty($request->input('signature'))) {
                $insertedFields = [
                    'signature' => $this->createImageFromBase64($doctor->signature, $request->input('signature'), 1),
                    'is_survey_completed' => 1,
                    'signature_date' => now(),
                    'updated_date' => now(),
                    'otp_verified' => 0,
                ];

                $updateStatus = DB::table('doctors')->where('id', $decryptedDoctorId)->update($insertedFields);

                if ($doctor->otp_verified == 1) {
                    return view('user.survey-complete', compact('doctor'));
                } else {
                    return redirect()->route('verify.mobile', $doc_id);
                }
            } else {
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.', 'error' => $e->getMessage(),], 500);
        }
    }

    private function sendDoctorOTP($doctor, $otp, $platform)
    {
        if (!empty($doctor->mobile) && $otp) {
            $mobile = $doctor->mobile;

            if (strlen($doctor->name) > 25) {
                $nameParts = explode(" ", $doctor->name);
                if (trim($nameParts[0]) === 'Dr.' || trim($nameParts[0]) === 'Dr') {
                    $name = trim($nameParts[0]) . '+' . trim($nameParts[1]) . '+' . substr(trim($nameParts[2]), 0, 1) . '.';
                } else {
                    $name = trim($nameParts[0]) . '+' . substr(trim($nameParts[1]), 0, 1) . '.';
                }
            } else {
                $name = str_replace(' ', '+', $doctor->name);
            }

            // Send OTP via SMS or WhatsApp based on platform
            if ($platform == 0) {
                // $api = "https://m1.sarv.com/api/v2.0/sms_campaign.php?token=YOUR_TOKEN&user_id=YOUR_USER_ID&route=TR&template_id=YOUR_TEMPLATE_ID&sender_id=imagic&language=EN&template=Dear+Doctor+Your+OTP+is+" . $otp . "&contact_numbers=" . $mobile;

                $api = "https://m1.sarv.com/api/v2.0/sms_campaign.php?token=18784455306578101d3da397.54383814&user_id=93984201&route=TR&template_id=12836&sender_id=imagic&language=EN&template=Dear+Doctor%0D%0A%0D%0AKindly+enter+below+OTP+to+submit+your+responses+for+the+Perception+Survey+study.%0D%0A%0D%0A" . $otp . "%0D%0A%0D%0ARgds%2C%0D%0AImagicahealth&contact_numbers=" . $mobile . "";
                $response = file_get_contents($api);
            } else {
                $toNumber = '91' . $mobile;
                $response = $this->sendWhatsAppMessage($toNumber, $otp);
            }
        }

        if (!empty($doctor->email) && ($otp != 0 || $otp != '')) {
            $fromEmail = "imagica.health@gmail.com";
            $toEmail = $doctor->email;
            $subject = "Alsign: OTP for Completion of Agreement Process";
            $name = $doctor->name;

            $curl = curl_init();
            $postData = json_encode([
                "mail_template_key" => "YOUR_TEMPLATE_KEY",
                "from" => [
                    "address" => "vanita@vinciohealth.in",
                    "name" => "Alsign"
                ],
                "to" => [
                    [
                        "email_address" => [
                            "address" => $toEmail,
                            "name" => $name
                        ]
                    ]
                ],
                "merge_info" => [
                    "subject" => $subject,
                    "name" => $name,
                    "OTP" => $otp,
                    "team" => "Alembic",
                    "product_name" => "Alsign"
                ]
            ]);

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.zeptomail.in/v1.1/email/template",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    "authorization: Zoho-enczapikey YOUR_API_KEY",
                    "cache-control: no-cache",
                    "content-type: application/json",
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return ['success' => false, 'message' => "cURL Error: " . $err];
            }
        }

        return ['success' => true];
    }

    public function getVerifyPage($doctor_id)
    {
        $user = Auth::guard('user')->user();
        $doc_id = $doctor_id;
        $decryptedDoctorId = Crypt::decryptString($doctor_id);
        $decryptedDoctorId = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;

        $doctor = DB::table('doctors')->where('id', $decryptedDoctorId)->where('is_deleted', 0)->first();
        $platform = DB::table('platformChange')->where('id', 1)->value('status');

        try {
            if ($doctor) {
                $survey_id = $doctor->survey_id;

                if ($doctor->otp == 0) {
                    $otp = $this->getOTP(4, false);
                    $sendResult = $this->sendDoctorOTP($doctor, $otp, $platform);

                    if (!$sendResult['success']) {
                        return response()->json(['error' => $sendResult['message']], 500);
                    }

                    DB::table('doctors')->where('id', $decryptedDoctorId)->update([
                        'otp' => $otp,
                        'otp_date' => now(),
                        'updated_date' => now()
                    ]);
                }

                $iSurvey = DB::table('survey')->where('survey_id', $survey_id)->first();

                if ($iSurvey) {
                    $data['survey'] = (array) $iSurvey;
                }
                $data['platform'] = $platform;

                return Inertia::render('User/VerifyMobile', [
                    'data' => $data,
                    'user' => $user,
                    'doc_id' => $doc_id,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.', 'error' => $e->getMessage(),], 500);
        }
    }

    public function resendOTP(Request $request, $doctor_id)
    {
        $user = Auth::guard('user')->user();
        $decryptedDoctorId = Crypt::decryptString($doctor_id);
        $decryptedDoctorId = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;

        $doctor = DB::table('doctors')->where('id', $decryptedDoctorId)->where('is_deleted', 0)->first();
        $platform = DB::table('platformChange')->where('id', 1)->value('status');

        if ($doctor) {
            $otp = $this->getOTP(4, false);
            $sendResult = $this->sendDoctorOTP($doctor, $otp, $platform);

            if (!$sendResult['success']) {
                return response()->json(['error' => $sendResult['message']], 500);
            }

            DB::table('doctors')->where('id', $decryptedDoctorId)->update([
                'otp' => $otp,
                'otp_date' => now(),
                'updated_date' => now()
            ]);

            return redirect()->back();
        }

        return response()->json(['success' => false, 'message' => 'Doctor not found.'], 404);
    }

    public function verifyOTP(Request $request, $doctor_id)
    {
        $doc_id =  $doctor_id;
        $decryptedDoctorId = Crypt::decryptString($doctor_id);
        $decryptedDoctorId = unserialize($decryptedDoctorId) ?? (int) $decryptedDoctorId;

        if (isset($decryptedDoctorId) && is_numeric($decryptedDoctorId)) {

            $doctor = DB::table('doctors')
                ->where('id', $decryptedDoctorId)
                ->where('is_deleted', 0)
                ->first();

            if ($doctor && !empty($request->otp)) {
                $survey_id = $doctor->survey_id;
                $masterOtps = DB::table('otp')->pluck('otp')->toArray();

                if ($doctor && !empty($request->otp)) {
                    $survey_id = $doctor->survey_id;

                    $masterOtps = DB::table('otp')->pluck('otp')->toArray();
                    $otpRecord = DB::table('doctors')
                        ->where('id', $decryptedDoctorId)
                        ->where('otp', $request->otp)
                        ->first();

                    if ($otpRecord) {
                        $otpCreatedTime = strtotime($otpRecord->otp_date);
                        $currentTime = time();
                        if (($currentTime - $otpCreatedTime) > 30) {
                            return redirect()->route('verify.mobile', $doc_id)
                                ->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
                        }
                    }

                    if ($request->otp == $doctor->otp || in_array($request->otp, $masterOtps)) {
                        DB::table('doctors')->where('id', $decryptedDoctorId)->update([
                            'otp_verified' => 1,
                            'updated_date' => now()
                        ]);

                        $encodedSurveyId = Crypt::encryptString($survey_id);
                        return redirect()->route('survey', ['survey_id' => $encodedSurveyId, 'doctor_id' => $doc_id]);
                    } else {
                        return redirect()->route('verify.mobile', $doc_id)
                            ->withErrors(['otp' => 'The entered OTP is incorrect. Please try again.']);
                    }
                } else {
                    return redirect()->route('verify.mobile', $doc_id)
                        ->withErrors(['otp' => 'Invalid request or OTP missing.']);
                }
            } else {
                return redirect()->route('verify.mobile', $doc_id);
            }
        }
    }
}
