@include('layout.header')
<style>
    <style>
        @page {
        padding: 85px 15px 30px 15px !important;
    }

    body {
        padding: 85px 15px 30px 15px !important;
        padding: 0 0 0px 0px;
        background: url("{{ isset($doctor->background) && $doctor->background ? $doctor->background : '' }}");
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

    .question_body {
        padding: 0;
    }
</style>
</style>
<div class="container-fluid custom-data p-0">
    <div class="wizard_container p-0">
        <div class="my-3 text-right">
            <img src="{{ $doctor->barcode }}" alt="" width="200">
        </div>
        <ul class="numeric-inside">
            <li><span>Agreement ID</span>: {{ $doctor->id }}</li>
            <li>
                <span>Survey status</span>:
                @php
                    $status = $doctor->status > 2 ? 'ZSM' : 'RSM';
                @endphp
                @if ($doctor->is_accept == 1 && $doctor->status >= 2)
                    <span class="text-success">{{ $status }} Approved</span>
                @elseif($doctor->is_accept == 0 && $doctor->status >= 2 && $doctor->is_hold == 0 && $doctor->is_reject == 1)
                    <span class="text-danger">{{ $status }} Rejected</span>
                @else
                    <span class="text-warning">{{ $status }} Pending</span>
                @endif
            </li>
            <li><span>Name of Doctor</span>: {{ $doctor->name }}</li>
            <li><span>Address</span>: {{ $doctor->address }}</li>
            <li><span>Pincode</span>: {{ $doctor->pin_code ?? '' }}</li>
            <li><span>Mobile</span>: {{ $doctor->mobile }}</li>
            <li><span>Email ID</span>: {{ $doctor->email }}</li>
            <li><span>Qualification</span>: {{ $doctor->qualification }}</li>
            <li><span>Registration No</span>: {{ $doctor->registration_no }}</li>
            <li><span>PAN No</span>: {{ $doctor->pan_number ?? '' }}</li>
            <li><span>GST</span>: {{ $doctor->gst }}</li>
            <li><span>Honorarium Amount</span>: {{ $doctor->honorarium }}</li>
            <li><span>P Code</span>: {{ $doctor->uin }}</li>
            <li><span>CRM No.</span>: {{ $doctor->crm_no }}</li>
            <li><span>Survey Code</span>: {{ $survey->urn }}</li>
            @if (!empty($doctor->cancel_cheque))
                <li><span>Cancel cheque photo</span> :
                    <b><a target="_blank" href="{{ asset('assets/img/doctor/document/' . $doctor->cancel_cheque) }}"
                            class="text-danger">Click to view</a></b>
                </li>
            @endif
        </ul>
        <div class="break-after">
        </div>
        <div class="div-aggrement p-5">
            <div class="custom-header">
                <h4 class="text-center heading"><b>CONSULTANCY AGREEMENT</b></h4>
            </div>
            <p class="mt-3">
                Dear Doctor,
            </p>
            <p class="line-height-2 text-align-justify">This Consultancy Agreement <b>(“Agreement”)</b> is made on
                <b>{{ \Carbon\Carbon::parse($doctor->agreement_date)->format('jS') }} day of
                    {{ \Carbon\Carbon::parse($doctor->agreement_date)->format("F'y") }}</b> by and between Alembic
                Pharmaceuticals
                Limited, a company incorporated under the laws of India and having its registered office at Alembic
                Road, Vadodara - 390 003, Gujarat, India (hereinafter referred to as <b>“Company”</b>),
                <b>{{ $doctor->name }}</b> , having PAN# <b>{{ $doctor->pan_number }}</b>, Registration No.
                <b>{{ $doctor->registration_no }}</b> and address at <b>{{ $doctor->address }}</b> ,
                <b>{{ $doctor->pin_code }}</b> India
                (hereinafter referred to as <b>“Consultant”</b>)
            </p>

            <p><b>RECITALS </b> :</p>

            <ul class="numeric-alphabet m-3 text-align-justify">
                <li><b>Whereas,</b> the Company is engaged in the business of the development, manufacturing, sale and
                    distribution of pharmaceutical, medicinal and veterinary products in India and abroad and has its
                    own research & development division.</li>
                <li><b>Whereas,</b> Consultant represents that Consultant is an independent healthcare practitioner and
                    has experience, data and updated knowledge on various practices, therapies and products and provides
                    consultancy services to pharmaceutical companies which will enable them to address changes in
                    product portfolio, current and ongoing approach to new product development, product stabilization,
                    efficacy of products, etc. </li>
                <li><b>Whereas,</b> both parties appreciates that healthcare is a very dynamic field which requires real
                    time and nuanced understanding of ailments, side effects, complications, responses etc. and these
                    needs to be monitored on a regular basis to address new drug development, getting market
                    intelligence, competitive advantage etc.</li>
                <li><b>Whereas,</b> Company agrees to avail the Services (defined below) from Consultant and Consultant
                    agrees to provide the same to Company in accordance with terms and conditions of this Agreement.
                </li>
            </ul>
            <p><b>NOW, THEREFORE, </b>both parties hereby agree as follows:</p>

            <ul class="numeric-decimal m-3 text-align-justify">

                <li>During the term of this Agreement, the Consultant shall provide specific services as more clearly
                    defined in Annexure 1 (“Services”) and the Company shall pay for the Services to the Consultant upon
                    Services rendered by the Consultant to the satisfaction of Company.</li>

                <li>The Agreement shall be effective upon execution by both parties and shall be valid till the
                    completion of Services or earlier terminated by mutual consent of both parties in writing. </li>
                <li>Consultant represents and warrants that (i) neither Consultant not its representatives are connected
                    directly or indirectly to any government organization/agency/undertaking in any manner; and (ii)
                    shall perform the Services in its individual capacity in compliance with all applicable laws, rules,
                    regulations and/or any guidelines, including but not limited to, National Medical Council Act, 2019,
                    Indian Medical Council (Professional Conduct, Etiquette and Ethics) Regulation, 2002 and Prevention
                    of Corruption Act, 1988. Consultant shall notify in advance to Company before entering into any kind
                    of relationship directly or indirectly with any government organization/agency/undertaking during
                    the validity of this Agreement and share copies of all permissions/NOC from the relevant government
                    organization/agency/undertaking required to perform Services under this Agreement by Consultant. The
                    manner in which Consultant renders Services will be within Consultant’s sole control, maintaining
                    his/her autonomy, integrity and discretion. </li>
                <li>Consultant shall be responsible for obtaining, maintaining, and/or displaying any and all
                    authorizations, licenses, registrations or other permissions necessary to carry out operations or
                    activities under this Agreement and also disclose and/or display its affiliations/Services herein to
                    maintain transparency. Failure to adhere to this clause 4 shall result in immediate termination of
                    the Agreement without any liability whatsoever.</li>
                <li>For performance under this Agreement, Consultant shall be compensated
                    Rs.<b>{{ $doctor->honorarium }}</b>/- (Rupees <b> {{ $doctor->honorarium }}
                        Only</b>) plus taxes for the
                    Services provided in Annexure 1. All fees shall be paid by Company after deduction of withholding
                    tax as per applicable tax laws. Upon deduction of withholding tax, Company will pay the amounts of
                    such taxes to the relevant governmental authority and transmit to the Consultant the copy of
                    official tax certificate or other evidence of such withholding tax.</li>
                <li>Consultant shall comply with all the compliance requirements under GST laws and do all things
                    necessary to enable Company to claim input tax credit in relation to GST laws payable under this
                    Agreement or in respect of Services provided under this Agreement. This shall include, but not
                    limited to, (i) issuing invoices/debit notes/revised invoices/credit notes as per the prescribed
                    format, containing all the information as is required for us to avail input tax credit; (ii) timely
                    submission of periodic statements/returns as per the GST laws within specified time lines with
                    complete and correct details as may be prescribed; (iii) timely issuance of debit note within the
                    prescribed time limit to enable us to take the credit; and (iv) timely payment of tax liability by
                    utilization of admissible credit or through cash. Further, any kind of Default in filing of GST
                    return or payment of GST will result into holding of payment by Company till cure of such default in
                    compliance with GST laws.</li>
                <li>Each party acknowledges that this Agreement is non-exclusive. If Company performs itself or retains
                    a third party to perform any services Consultant will cooperate and coordinate with Company or such
                    third party as reasonably requested or required by such third parties to perform their duties.</li>
                <li>Consultant shall not dispense any patient data (personal information of patient) in contravention of
                    Information Technology Act, 2000, Health Data Management Policy, 2020, Electronic Health Records
                    (EHR) Standards, 2016 and all other applicable laws prevailing from time to time which protect the
                    privacy and interests of patients.</li>
                <li>Company shall have the right to terminate this Agreement with immediate effect by notice in case of
                    violation of this Agreement by Consultant. Notwithstanding anything mentioned in this Agreement.
                    Company shall have the right to terminate this Agreement for convenience at any time upon giving
                    prior written notice to Consultant. In case of such termination, Company will be only liable to pay
                    for the Services rendered till the date of the termination. Upon termination of this Agreement for
                    any reason, it shall not affect or prejudice any rights which may have accrued to the party prior to
                    the expiration or termination of this Agreement.</li>
                <li>All information disclosed by Company to Consultant in connection with this Agreement and other
                    related trade secrets, specifications, technology, know-how and other confidential and proprietary
                    information of Company (collectively, the “Confidential Information”) is and will be kept
                    confidential by Consultant and not used by Consultant other than in connection with this Agreement,
                    except to the extent such Confidential Information (i) becomes lawfully obtainable from other
                    sources, (ii) is or becomes part of the public domain (other than act or omission of the
                    Consultant),and/or (iii) is required by any Authority under any applicable law or regulation,
                    provided however that Consultant shall consult with Company as to the contents of such disclosure.
                    On termination of this Agreement for any reason whatsoever, (i) all Confidential Information and any
                    documents received (including copies thereof) shall be returned by Consultant to Company without
                    retaining any copy in any manner, and (ii) these confidentiality and non-use obligations of
                    Consultant shall survive the termination of this Agreement. </li>
                <li>A complete set of all data including information, and advices provided by the Consultant shall be
                    made available to Company upon request and/or upon completion or termination of such Service, and
                    such data shall be the sole property of Company. </li>
                <li>This Agreement is governed by and shall be construed in accordance with laws of India. All disputes,
                    controversies and claims arising out of or in connection with this Agreement shall be brought
                    exclusively before a court of competent jurisdiction in Vadodara, Gujarat and both parties consents
                    to the exclusive jurisdiction and venue of such court.</li>
                <li>It is expressly acknowledged and agreed by both parties that neither this Agreement nor any payment
                    hereunder is in exchange for any explicit or implicit agreement or understanding that the Consultant
                    will purchase, order, prescribe, recommend or otherwise arrange for, or provide preferential
                    treatment for the Company’s products. Furthermore, the total payment for the Services represents the
                    fair market value for the Services and has not been determined in any manner that takes into account
                    the volume or value of any prescriptions, referrals or business between both parties. </li>
                <li>This Agreement does not create any other business arrangement, including but not limited to any
                    partnership, agency or joint venture, between both parties. </li>
                <li>This Agreement, together with annexure attached hereto, forms the entire agreement between both
                    parties and supersedes all earlier negotiations, discussions, agreements and understandings between
                    both parties. The recitals above are incorporated herein by this reference and forms an integral
                    part of this Agreement This Agreement shall not be changed, modified, amended or supplemented except
                    by a written instrument duly signed by both parties. Neither party shall have the authority to make
                    any statements, representations or commitments of any kind, or to take any action, which shall be
                    binding on the other party, without the prior written consent of such other party.</li>
                <li>Each party will use its commercially reasonable efforts in good faith to adhere to its
                    representations mentioned in this agreement.</li>
                <li>This Agreement is personal and shall not be assigned by Consultant. The Consultant has the full
                    power and authority to enter into this Agreement and to perform the Services under this Agreement.
                </li>
                <li>Consultant shall indemnify and hold harmless Company from and against all claims, losses, damages,
                    attorney fees, and/or cost & expenses arising out of or in connection with breach of this Agreement
                    and/or misrepresentation, negligence, fraud or willful misconduct by the Consultant or its
                    representatives. </li>
                <li>The Consultant consents to photographing and audio and video recording of the proceedings of
                    Consultant’s oration/ speaking session forming part of Services (collectively, “Recordings”) by
                    Company or its representatives, at Company’s discretion. Any intellectual property generated from
                    the Services provided by the Consultant, including the Recordings, shall vest exclusively with the
                    Company, unless otherwise agreed to the contrary in writing by both parties. All such intellectual
                    property and all materials and information made or developed by the Consultant in connection with
                    the Services provided will be sole property of the Company and the Company shall have the right to
                    use such materials and information in any manner whatsoever.</li>
                <li>Company is committed to protect Consultant’s sensitive personal information, which is why access to
                    such information is being limited to authorized employees, agents, contractors, third parties,
                    affiliates or others on a need to know basis only. </li>
                <li>All notices required hereunder shall be given by (i) in writing and personally delivered, (ii) sent
                    by courier (charges prepaid) or (iii) registered mail with return receipt requested or speed post,
                    and addressed to the parties mentioned above, or at such other address as any party shall hereafter
                    inform the other party by written notice given as aforesaid. All written notices so given shall be
                    deemed effective upon receipt.</li>
                <li>This Agreement has been jointly prepared on the basis of the mutual understanding of both parties
                    and shall not be construed against either party by reason of such party’s being the drafter hereof
                    or thereof.</li>
                <li>This Agreement is executed in counterparts, each of which shall be deemed to be an original and both
                    of which together shall constitute one and same agreement. An executed copy of this Agreement may be
                    delivered by electronic mail in “portable document format” (“.pdf”), or by any other electronic
                    means intended to preserve the original graphic and pictorial appearance of a document, shall
                    constitute effective execution and delivery of this Agreement as to both parties and to be used in
                    lieu of the original Agreement for all purposes. Both parties acknowledge and agree that this
                    agreement may be executed by digital signature or electronic signature, which shall be considered as
                    an original signature for all purposes and shall have the same force and effect as an original
                    signature.</li>
            </ul>
            <p class="line-height-2"><b>IN WITNESS WHEREOF</b>, both parties have executed this Agreement with effect
                from the last date and year written below.</p>
            <br />
            <p class="line-height-2"><b>Alembic Pharmaceuticals Limited,</b></p>
        </div>
        <table class="m-3 w-100 p-5">
            <tbody>
                <tr>
                    <td width="50%">
                        <div class="p-2">
                            <h6 class="border-bottom">
                                @if (!empty($doctor->head_signature))
                                    <img src="{{ $doctor->head_signature }}" alt="" width="250px"
                                        height="100px">
                                @endif
                            </h6>
                        </div>
                        <h6><b>Name :</b> {{ $doctor->head_name }}</h6>
                        <h6><b>Title :</b> {{ $doctor->head_title }}</h6>
                        <h6><b>Division :</b> {{ $doctor->head_division }}</h6>
                        <h6><b>Date :</b>{{ \Carbon\Carbon::parse($doctor->agreement_date)->format('jS M Y') }}</h6>
                    </td>
                    <td width="50%">
                        <div class="p-2">
                            <h6 class="border-bottom">
                                @if (!empty($doctor->signature))
                                    <img src="{{ $doctor->signature }}" alt="" width="250px" height="100px">
                                @endif
                            </h6>
                        </div>
                        <h6><b>Dr. Name :</b> {{ $doctor->name }}</h6>
                        <h6><b>Date :</b>{{ \Carbon\Carbon::parse($doctor->signature_date)->format('jS M Y') }}</h6>
                        <h6 style="visibility: hidden;">..</h6>
                        <h6 style="visibility: hidden;">..</h6>

                    </td>
                </tr>
            </tbody>
        </table>
        <div class="break-after">
        </div>
        <div class="div-aggrement p-5">
            <div class="custom-header">
                <h4 class="text-center heading"><b>ANNEXURE 1</b></h4>
            </div>
            <div class="custom-header">
                <h4 class="text-center heading"><b>SERVICES</b></h4>
            </div>
            <div class="border-1">
                <ul class="numeric-alphabet3 m-3 text-align-justify">
					<li>Advisory service to re-orient or re-organize portfolio of target and to give priority in terms of time and resources.</li>
					<li>Guidance on brand positioning.</li>
					<li>Evaluation of Product, literature and labels in pipeline, current product portfolio to suggest new products.</li>
					</li>
				</ul>
            </div>
            <p><b>Note:</b> Upon completion of Services as per Company's satisfaction, Company shall pay to the
                Consultant Service fees as mentioned above or as mutually agreed, in writing, between both parties.
            </p>
        </div>
        <div class="break-after">
        </div>

        <!-- <div class="break-after">

  </div> -->

        @php
            // Determine Doctor Status
            $status_text = 'Pending';
            if ($doctor->is_accept == 1) {
                $status_text = 'Accepted';
            } elseif ($doctor->is_reject == 1) {
                $status_text = 'Rejected';
            } elseif ($doctor->is_hold == 1) {
                $status_text = 'Hold';
            }

            // Default status values
            $status_amdmrmsm = 'Pending';
            $status_marketing = 'Pending';
            $status_medical = 'Pending';
            $status_account = 'Pending';

            // AMDMRMSM Status
            if ($doctor->status <= 4) {
                if ($status_text == 'Accepted') {
                    $status_amdmrmsm = 'Accepted - ' . date('d M, Y g:i A', strtotime($doctor->accept_date));
                } elseif ($status_text == 'Rejected') {
                    $status_amdmrmsm = 'Rejected - ' . date('d M, Y g:i A', strtotime($doctor->reject_date));
                } elseif ($status_text == 'Hold') {
                    $status_amdmrmsm = 'Hold - ' . date('d M, Y g:i A', strtotime($doctor->hold_date));
                }
            } elseif ($doctor->status > 4) {
                $status_amdmrmsm =
                    $status_text == 'Hold'
                        ? 'Pending'
                        : 'Accepted - ' . date('d M, Y g:i A', strtotime($doctor->accept_date));
            }

            // Marketing Status
            if ($doctor->status == 5) {
                if ($status_text == 'Accepted') {
                    $status_marketing = 'Accepted - ' . date('d M, Y g:i A', strtotime($doctor->marketing_accept_date));
                } elseif ($status_text == 'Rejected') {
                    $status_marketing = 'Rejected - ' . date('d M, Y g:i A', strtotime($doctor->marketing_reject_date));
                } elseif ($status_text == 'Hold') {
                    $status_marketing = 'Hold - ' . date('d M, Y g:i A', strtotime($doctor->marketing_hold_date));
                }
            } elseif ($doctor->status > 5) {
                $status_marketing =
                    $status_text == 'Hold'
                        ? 'Pending'
                        : 'Accepted - ' . date('d M, Y g:i A', strtotime($doctor->marketing_accept_date));
            }

            // Medical Status
            if ($doctor->status == 6) {
                if ($status_text == 'Accepted') {
                    $status_medical = 'Accepted - ' . date('d M, Y g:i A', strtotime($doctor->medical_accept_date));
                } elseif ($status_text == 'Rejected') {
                    $status_medical = 'Rejected - ' . date('d M, Y g:i A', strtotime($doctor->medical_reject_date));
                } elseif ($status_text == 'Hold') {
                    $status_medical = 'Hold - ' . date('d M, Y g:i A', strtotime($doctor->medical_hold_date));
                }
            } elseif ($doctor->status > 6) {
                $status_medical =
                    $status_text == 'Hold'
                        ? 'Pending'
                        : 'Accepted - ' . date('d M, Y g:i A', strtotime($doctor->medical_accept_date));
            }

            // Account Status
            if ($doctor->status >= 7) {
                if ($status_text == 'Accepted') {
                    $status_account = 'Accepted - ' . date('d M, Y g:i A', strtotime($doctor->account_accept_date));
                } elseif ($status_text == 'Rejected') {
                    $status_account = 'Rejected - ' . date('d M, Y g:i A', strtotime($doctor->account_reject_date));
                } elseif ($status_text == 'Hold') {
                    $status_account = 'Hold - ' . date('d M, Y g:i A', strtotime($doctor->account_hold_date));
                }
            }
        @endphp
        @if (!empty($doctor->receiving_signature))
            <div class="div-aggrement p-5">
                <div class="break-after"></div>
                <div class="custom-header">
                    <h4 class="text-center heading"><b>RECEIVING</b></h4>
                </div>
                <div class="body mt-3">
                    <p> I here by confirm that I have received <b>RS.{{ $doctor->honorarium }}</b> for the survey
                        participation
                        remuneration.</p>
                    <table class=" w-100">
                        <tbody>
                            <tr>
                                <td width="50%">
                                    <div class="p-2">
                                        <h6 class="border-bottom">
                                            @if (!empty($doctor->receiving_signature))
                                                <img src="{{ $doctor->receiving_signature }}" width="150"
                                                    alt="Doctor Signature" />
                                            @endif
                                        </h6>
                                    </div>
                                    <h6><b>Dr. Name :</b> {{ $doctor->name }}</h6>
                                    <h6><b>Dr. Place :</b> {{ $doctor->address }}@if (!empty($doctor->pin_code))
                                            , {{ $doctor->pin_code }}
                                        @endif
                                    </h6>
                                    <h6><b>Date :</b>
                                        {{ \Carbon\Carbon::parse($doctor->payment_received_date)->format('jS M Y') }}
                                    </h6>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
