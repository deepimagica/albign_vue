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

class DashboardController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        $userId = $user->id;
        $employeePos = $user->employee_pos;
        $surveyType = $request->get('dashboard', 0);
        $search = $request->get('search', '');
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

    public function storeAgreementData(Request $request, $doctor_id)
    {
        $data = $this->decryptData($request->data);
        $decryptedDoctorId = Crypt::decrypt($data['doctor_id']);
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
}
