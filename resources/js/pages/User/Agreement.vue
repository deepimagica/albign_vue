<script setup>
import TopNav from "../Layout/TopNav.vue";
import { useAgreement } from "../../composables/useAgreeent";

const {
  response,
  decodedDoctorId,
  csrfToken,
  errors,
  isAgree,
  formattedAgreementDate,
  submitForm
} = useAgreement();
</script>

<template>
  <TopNav />
  <main class="py-4">
    <div class="container full-height">
      <div class="row row-height">
        <div class="col-lg-12 content-right" id="start">
          <div id="wizard_container">
            <div class="nine">
              <h1>Survey<span>Agreement</span></h1>
            </div>
            <form v-if="response.survey?.agreement_type === 0" @submit.prevent="submitForm" method="POST"
              id="agreement">
              <input type="hidden" name="_token" :value="csrfToken" />
              <input type="hidden" name="doctor_id" :value="response.doctor?.id" />

              <div id="middle-wizard">
                <div class="step">
                  <div class="col-12 dash-div">
                    <div class="login-form domy-text">
                      <h4 class="text-center mb-5 heading"><b>CONSULTANCY AGREEMENT</b></h4>
                      <p class="my-2">
                        This Consultancy Agreement (<b>“Agreement”</b>) is made on
                        <b>{{ formattedAgreementDate }}</b>
                        by and between Alembic Pharmaceuticals Limited, a company incorporated
                        under the laws of India and having its registered office at Alembic Road,
                        Vadodara - 390 003, Gujarat, India (hereinafter referred to as <b>“Company”</b>),
                        <b>{{ response.doctor?.name || 'N/A' }}</b>, having PAN#
                        <b>{{ response.doctor?.pan_number || 'N/A' }}</b>,
                        Registration No. <b>{{ response.doctor?.registration_no || 'N/A' }}</b> and
                        address at
                        <b>{{ response.doctor?.address || 'N/A' }}</b>,
                        <b>{{ response.doctor?.pin_code || 'N/A' }}</b>, India (hereinafter
                        referred to as <b>“Consultant”</b>).
                      </p>
                      <p><b>RECITALS </b> :</p>
                      <ul class="numeric-alpha m-3 text-align-justify">
                        <li><b>Whereas,</b> the Company is engaged in the business of the
                          development, manufacturing, sale and distribution of pharmaceutical,
                          medicinal and veterinary products in India and abroad and has its
                          own
                          research & development division.</li>
                        <li><b>Whereas,</b> Consultant represents that Consultant is an
                          independent
                          healthcare practitioner and has experience, data and updated
                          knowledge
                          on various practices, therapies and products and provides
                          consultancy
                          services to pharmaceutical companies which will enable them to
                          address
                          changes in product portfolio, current and ongoing approach to new
                          product development, product stabilization, efficacy of products,
                          etc.
                        </li>
                        <li><b>Whereas,</b> both parties appreciates that healthcare is a very
                          dynamic field which requires real time and nuanced understanding of
                          ailments, side effects, complications, responses etc. and these
                          needs to
                          be monitored on a regular basis to address new drug development,
                          getting
                          market intelligence, competitive advantage etc.</li>
                        <li><b>Whereas,</b> Company agrees to avail the Services (defined below)
                          from Consultant and Consultant agrees to provide the same to Company
                          in
                          accordance with terms and conditions of this Agreement.
                        </li>
                      </ul>
                      <p><b>NOW, THEREFORE, </b>both parties hereby agree as follows:</p>
                      <ul class="numeric-ul p-17">
                        <li>During the term of this Agreement, the Consultant shall provide
                          specific
                          services as more clearly defined in Annexure 1 (“Services”) and the
                          Company shall pay for the Services to the Consultant upon Services
                          rendered by the Consultant to the satisfaction of Company.</li>
                        <li>The Agreement shall be effective upon execution by both parties and
                          shall be valid till the completion of Services or earlier terminated
                          by
                          mutual consent of both parties in writing.</li>
                        <li>Consultant represents and warrants that (i) neither Consultant not
                          its
                          representatives are connected directly or indirectly to any
                          government
                          organization/agency/undertaking in any manner; and (ii) shall
                          perform
                          the Services in its individual capacity in compliance with all
                          applicable laws, rules, regulations and/or any guidelines, including
                          but
                          not limited to, National Medical Council Act, 2019, Indian Medical
                          Council (Professional Conduct, Etiquette and Ethics) Regulation,
                          2002
                          and Prevention of Corruption Act, 1988. Consultant shall notify in
                          advance to Company before entering into any kind of relationship
                          directly or indirectly with any government
                          organization/agency/undertaking during the validity of this
                          Agreement
                          and share copies of all permissions/NOC from the relevant government
                          organization/agency/undertaking required to perform Services under
                          this
                          Agreement by Consultant. The manner in which Consultant renders
                          Services
                          will be within Consultant's sole control, maintaining his/her
                          autonomy,
                          integrity and discretion.</li>
                        <li>Consultant shall be responsible for obtaining, maintaining, and/or
                          displaying any and all authorizations, licenses, registrations or
                          other
                          permissions necessary to carry out operations or activities under
                          this
                          Agreement and also disclose and/or display its affiliations/Services
                          herein to maintain transparency. Failure to adhere to this clause 4
                          shall result in immediate termination of the Agreement without any
                          liability whatsoever.</li>
                        <li>For performance under this Agreement, Consultant shall be
                          compensated
                          Rs.<b>{{ response.doctor?.honorarium }}</b>/- (Rupees <b>
                            {{ response.doctor?.convertedAmount }}
                            Only</b>) plus taxes for the Services provided in Annexure 1.
                          All
                          fees shall be paid by Company after deduction of withholding tax as
                          per
                          applicable tax laws. Upon deduction of withholding tax, Company will
                          pay
                          the amounts of such taxes to the relevant governmental authority and
                          transmit to the Consultant the copy of official tax certificate or
                          other
                          evidence of such withholding tax.</li>
                        <li>Consultant shall comply with all the compliance requirements under
                          GST
                          laws and do all things necessary to enable Company to claim input
                          tax
                          credit in relation to GST laws payable under this Agreement or in
                          respect of Services provided under this Agreement. This shall
                          include,
                          but not limited to, (i) issuing invoices/debit notes/revised
                          invoices/credit notes as per the prescribed format, containing all
                          the
                          information as is required for us to avail input tax credit; (ii)
                          timely
                          submission of periodic statements/returns as per the GST laws within
                          specified time lines with complete and correct details as may be
                          prescribed; (iii) timely issuance of debit note within the
                          prescribed
                          time limit to enable us to take the credit; and (iv) timely payment
                          of
                          tax liability by utilization of admissible credit or through cash.
                          Further, any kind of Default in filing of GST return or payment of
                          GST
                          will result into holding of payment by Company till cure of such
                          default
                          in compliance with GST laws.</li>
                        <li>Each party acknowledges that this Agreement is non-exclusive. If
                          Company
                          performs itself or retains a third party to perform any services
                          Consultant will cooperate and coordinate with Company or such third
                          party as reasonably requested or required by such third parties to
                          perform their duties.</li>
                        <li>Consultant shall not dispense any patient data (personal information
                          of
                          patient) in contravention of Information Technology Act, 2000,
                          Health
                          Data Management Policy, 2020, Electronic Health Records (EHR)
                          Standards,
                          2016 and all other applicable laws prevailing from time to time
                          which
                          protect the privacy and interests of patients.</li>
                        <li>Company shall have the right to terminate this Agreement with
                          immediate
                          effect by notice in case of violation of this Agreement by
                          Consultant.
                          Notwithstanding anything mentioned in this Agreement. Company shall
                          have
                          the right to terminate this Agreement for convenience at any time
                          upon
                          giving prior written notice to Consultant. In case of such
                          termination,
                          Company will be only liable to pay for the Services rendered till
                          the
                          date of the termination. Upon termination of this Agreement for any
                          reason, it shall not affect or prejudice any rights which may have
                          accrued to the party prior to the expiration or termination of this
                          Agreement.</li>
                        <li>All information disclosed by Company to Consultant in connection
                          with
                          this Agreement and other related trade secrets, specifications,
                          technology, know-how and other confidential and proprietary
                          information
                          of Company (collectively, the “Confidential Information”) is and
                          will be
                          kept confidential by Consultant and not used by Consultant other
                          than in
                          connection with this Agreement, except to the extent such
                          Confidential
                          Information (i) becomes lawfully obtainable from other sources, (ii)
                          is
                          or becomes part of the public domain (other than act or omission of
                          the
                          Consultant),and/or (iii) is required by any Authority under any
                          applicable law or regulation, provided however that Consultant shall
                          consult with Company as to the contents of such disclosure. On
                          termination of this Agreement for any reason whatsoever, (i) all
                          Confidential Information and any documents received (including
                          copies
                          thereof) shall be returned by Consultant to Company without
                          retaining
                          any copy in any manner, and (ii) these confidentiality and non-use
                          obligations of Consultant shall survive the termination of this
                          Agreement. </li>
                        <li>A complete set of all data including information, and advices
                          provided
                          by the Consultant shall be made available to Company upon request
                          and/or
                          upon completion or termination of such Service, and such data shall
                          be
                          the sole property of Company. </li>
                        <li>This Agreement is governed by and shall be construed in accordance
                          with
                          laws of India. All disputes, controversies and claims arising out of
                          or
                          in connection with this Agreement shall be brought exclusively
                          before a
                          court of competent jurisdiction in Vadodara, Gujarat and both
                          parties
                          consents to the exclusive jurisdiction and venue of such court.</li>
                        <li>It is expressly acknowledged and agreed by both parties that neither
                          this Agreement nor any payment hereunder is in exchange for any
                          explicit
                          or implicit agreement or understanding that the Consultant will
                          purchase, order, prescribe, recommend or otherwise arrange for, or
                          provide preferential treatment for the Company’s products.
                          Furthermore,
                          the total payment for the Services represents the fair market value
                          for
                          the Services and has not been determined in any manner that takes
                          into
                          account the volume or value of any prescriptions, referrals or
                          business
                          between both parties.</li>
                        <li>This Agreement does not create any other business arrangement,
                          including
                          but not limited to any partnership, agency or joint venture, between
                          both parties. </li>
                        <li>This Agreement, together with annexure attached hereto, forms the
                          entire
                          agreement between both parties and supersedes all earlier
                          negotiations,
                          discussions, agreements and understandings between both parties. The
                          recitals above are incorporated herein by this reference and forms
                          an
                          integral part of this Agreement This Agreement shall not be changed,
                          modified, amended or supplemented except by a written instrument
                          duly
                          signed by both parties. Neither party shall have the authority to
                          make
                          any statements, representations or commitments of any kind, or to
                          take
                          any action, which shall be binding on the other party, without the
                          prior
                          written consent of such other party.</li>
                        <li>Each party will use its commercially reasonable efforts in good
                          faith to
                          adhere to its representations mentioned in this agreement.</li>
                        <li>This Agreement is personal and shall not be assigned by Consultant.
                          The
                          Consultant has the full power and authority to enter into this
                          Agreement
                          and to perform the Services under this Agreement.</li>
                        <li>Consultant shall indemnify and hold harmless Company from and
                          against
                          all claims, losses, damages, attorney fees, and/or cost & expenses
                          arising out of or in connection with breach of this Agreement and/or
                          misrepresentation, negligence, fraud or willful misconduct by the
                          Consultant or its representatives. </li>
                        <li>The Consultant consents to photographing and audio and video
                          recording
                          of the proceedings of Consultant’s oration/ speaking session forming
                          part of Services (collectively, “Recordings”) by Company or its
                          representatives, at Company’s discretion. Any intellectual property
                          generated from the Services provided by the Consultant, including
                          the
                          Recordings, shall vest exclusively with the Company, unless
                          otherwise
                          agreed to the contrary in writing by both parties. All such
                          intellectual
                          property and all materials and information made or developed by the
                          Consultant in connection with the Services provided will be sole
                          property of the Company and the Company shall have the right to use
                          such
                          materials and information in any manner whatsoever.</li>
                        <li>Company is committed to protect Consultant’s sensitive personal
                          information, which is why access to such information is being
                          limited to
                          authorized employees, agents, contractors, third parties, affiliates
                          or
                          others on a need to know basis only. </li>
                        <li>All notices required hereunder shall be given by (i) in writing and
                          personally delivered, (ii) sent by courier (charges prepaid) or
                          (iii)
                          registered mail with return receipt requested or speed post, and
                          addressed to the parties mentioned above, or at such other address
                          as
                          any party shall hereafter inform the other party by written notice
                          given
                          as aforesaid. All written notices so given shall be deemed effective
                          upon receipt.</li>
                        <li>This Agreement has been jointly prepared on the basis of the mutual
                          understanding of both parties and shall not be construed against
                          either
                          party by reason of such party’s being the drafter hereof or thereof.
                        </li>
                        <li>This Agreement is executed in counterparts, each of which shall be
                          deemed to be an original and both of which together shall constitute
                          one
                          and same agreement. An executed copy of this Agreement may be
                          delivered
                          by electronic mail in “portable document format” (“.pdf”), or by any
                          other electronic means intended to preserve the original graphic and
                          pictorial appearance of a document, shall constitute effective
                          execution
                          and delivery of this Agreement as to both parties and to be used in
                          lieu
                          of the original Agreement for all purposes. Both parties acknowledge
                          and
                          agree that this agreement may be executed by digital signature or
                          electronic signature, which shall be considered as an original
                          signature
                          for all purposes and shall have the same force and effect as an
                          original
                          signature.</li>
                      </ul>
                      <p class="m-2">Thanking you,</p>
                      <div class="col-md-12">
                        <div class="my-2">
                          <label class="container_check mt-3 text-left">I Accept
                            <input type="checkbox" class="checkbox-one" name="is_agree" v-model="isAgree"
                              :checked="isAgree">
                            <span class="checkmark"></span>
                          </label>
                          <span v-if="errors.is_agree" class="text-danger">{{ errors.is_agree }}</span>
                        </div>
                        <div class="row mx-auto justify-content-center">
                          <div class="col-md-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary w-100 btn-1">BACK</a>
                          </div>
                          <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 btn-2">SUBMIT</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <form v-if="response.survey?.agreement_type === 1" @submit.prevent="submitForm" method="POST"
              id="agreement">
              <input type="hidden" name="_token" :value="csrfToken" />
              <input type="hidden" name="doctor_id" :value="response.doctor?.id" />
              <div id="middle-wizard">
                <div class="step">
                  <div class="col-12 dash-div">
                    <div class="login-form domy-text">
                      <h4 class="text-center mb-5 heading"><b>CONSULTANCY AGREEMENT</b></h4>
                      <p class="my-2">
                        This Consultancy Agreement (<b>“Agreement”</b>) is made on
                        <b>{{ formattedAgreementDate }}</b>
                        by and between Alembic Pharmaceuticals Limited, a company incorporated
                        under
                        the laws of India and having its registered office at Alembic Road,
                        Vadodara
                        - 390 003, Gujarat, India (hereinafter referred to as <b>“Company”</b>),
                        <b>{{ response.doctor?.name }}</b> , having PAN#
                        <b>{{ response.doctor?.pan_number }}</b>,
                        Registration No. <b>{{ response.doctor?.registration_no }}</b> and
                        address at
                        <b>{{ response.doctor?.address }}</b> ,
                        <b>{{ response.doctor?.pin_code }}</b> India (hereinafter
                        referred to as <b>“Consultant”</b>)
                      </p>
                      <p><b>RECITALS </b> :</p>
                      <ul class="numeric-alpha m-3 text-align-justify">
                        <li><b>Whereas,</b> the Company is engaged in the business of the
                          development, manufacturing, sale and distribution of pharmaceutical,
                          medicinal and veterinary products in India and abroad and has its
                          own
                          research & development division.</li>
                        <li><b>Whereas,</b> Consultant represents that Consultant is an
                          independent
                          healthcare practitioner and has experience, data and updated
                          knowledge
                          on various practices, therapies and products and provides
                          consultancy
                          services to pharmaceutical companies which will enable them to
                          address
                          changes in product portfolio, current and ongoing approach to new
                          product development, product stabilization, efficacy of products,
                          etc.
                        </li>
                        <li><b>Whereas,</b> both parties appreciates that healthcare is a very
                          dynamic field which requires real time and nuanced understanding of
                          ailments, side effects, complications, responses etc. and these
                          needs to
                          be monitored on a regular basis to address new drug development,
                          getting
                          market intelligence, competitive advantage etc.</li>
                        <li><b>Whereas,</b> Company agrees to avail the Services (defined below)
                          from Consultant and Consultant agrees to provide the same to Company
                          in
                          accordance with terms and conditions of this Agreement.
                        </li>
                      </ul>
                      <p><b>NOW, THEREFORE, </b>both parties hereby agree as follows:</p>
                      <ul class="numeric-ul p-17">
                        <li>During the term of this Agreement, the Consultant shall provide
                          specific
                          services as more clearly defined in Annexure 1 (“Services”) and the
                          Company shall pay for the Services to the Consultant upon Services
                          rendered by the Consultant to the satisfaction of Company.</li>
                        <li>The Agreement shall be effective upon execution by both parties and
                          shall be valid till the completion of Services or earlier terminated
                          by
                          mutual consent of both parties in writing.</li>
                        <li>Consultant represents and warrants that (i) neither Consultant not
                          its
                          representatives are connected directly or indirectly to any
                          government
                          organization/agency/undertaking in any manner; and (ii) shall
                          perform
                          the Services in its individual capacity in compliance with all
                          applicable laws, rules, regulations and/or any guidelines, including
                          but
                          not limited to, National Medical Council Act, 2019, Indian Medical
                          Council (Professional Conduct, Etiquette and Ethics) Regulation,
                          2002
                          and Prevention of Corruption Act, 1988. Consultant shall notify in
                          advance to Company before entering into any kind of relationship
                          directly or indirectly with any government
                          organization/agency/undertaking during the validity of this
                          Agreement
                          and share copies of all permissions/NOC from the relevant government
                          organization/agency/undertaking required to perform Services under
                          this
                          Agreement by Consultant. The manner in which Consultant renders
                          Services
                          will be within Consultant’s sole control, maintaining his/her
                          autonomy,
                          integrity and discretion.</li>
                        <li>Consultant shall be responsible for obtaining, maintaining, and/or
                          displaying any and all authorizations, licenses, registrations or
                          other
                          permissions necessary to carry out operations or activities under
                          this
                          Agreement and also disclose and/or display its affiliations/Services
                          herein to maintain transparency. Failure to adhere to this clause 4
                          shall result in immediate termination of the Agreement without any
                          liability whatsoever.</li>
                        <li>For performance under this Agreement, Consultant shall be
                          compensated
                          Rs.<b>{{ response.doctor?.honorarium }}</b>/- (Rupees <b>
                            {{ response.doctor?.convertedAmount }}
                            Only</b>) plus taxes for the Services provided in Annexure 1.
                          All
                          fees shall be paid by Company after deduction of withholding tax as
                          per
                          applicable tax laws. Upon deduction of withholding tax, Company will
                          pay
                          the amounts of such taxes to the relevant governmental authority and
                          transmit to the Consultant the copy of official tax certificate or
                          other
                          evidence of such withholding tax.</li>
                        <li>Consultant shall comply with all the compliance requirements under
                          GST
                          laws and do all things necessary to enable Company to claim input
                          tax
                          credit in relation to GST laws payable under this Agreement or in
                          respect of Services provided under this Agreement. This shall
                          include,
                          but not limited to, (i) issuing invoices/debit notes/revised
                          invoices/credit notes as per the prescribed format, containing all
                          the
                          information as is required for us to avail input tax credit; (ii)
                          timely
                          submission of periodic statements/returns as per the GST laws within
                          specified time lines with complete and correct details as may be
                          prescribed; (iii) timely issuance of debit note within the
                          prescribed
                          time limit to enable us to take the credit; and (iv) timely payment
                          of
                          tax liability by utilization of admissible credit or through cash.
                          Further, any kind of Default in filing of GST return or payment of
                          GST
                          will result into holding of payment by Company till cure of such
                          default
                          in compliance with GST laws.</li>
                        <li>Each party acknowledges that this Agreement is non-exclusive. If
                          Company
                          performs itself or retains a third party to perform any services
                          Consultant will cooperate and coordinate with Company or such third
                          party as reasonably requested or required by such third parties to
                          perform their duties.</li>
                        <li>Consultant shall not dispense any patient data (personal information
                          of
                          patient) in contravention of Information Technology Act, 2000,
                          Health
                          Data Management Policy, 2020, Electronic Health Records (EHR)
                          Standards,
                          2016 and all other applicable laws prevailing from time to time
                          which
                          protect the privacy and interests of patients.</li>
                        <li>Company shall have the right to terminate this Agreement with
                          immediate
                          effect by notice in case of violation of this Agreement by
                          Consultant.
                          Notwithstanding anything mentioned in this Agreement. Company shall
                          have
                          the right to terminate this Agreement for convenience at any time
                          upon
                          giving prior written notice to Consultant. In case of such
                          termination,
                          Company will be only liable to pay for the Services rendered till
                          the
                          date of the termination. Upon termination of this Agreement for any
                          reason, it shall not affect or prejudice any rights which may have
                          accrued to the party prior to the expiration or termination of this
                          Agreement.</li>
                        <li>All information disclosed by Company to Consultant in connection
                          with
                          this Agreement and other related trade secrets, specifications,
                          technology, know-how and other confidential and proprietary
                          information
                          of Company (collectively, the “Confidential Information”) is and
                          will be
                          kept confidential by Consultant and not used by Consultant other
                          than in
                          connection with this Agreement, except to the extent such
                          Confidential
                          Information (i) becomes lawfully obtainable from other sources, (ii)
                          is
                          or becomes part of the public domain (other than act or omission of
                          the
                          Consultant),and/or (iii) is required by any Authority under any
                          applicable law or regulation, provided however that Consultant shall
                          consult with Company as to the contents of such disclosure. On
                          termination of this Agreement for any reason whatsoever, (i) all
                          Confidential Information and any documents received (including
                          copies
                          thereof) shall be returned by Consultant to Company without
                          retaining
                          any copy in any manner, and (ii) these confidentiality and non-use
                          obligations of Consultant shall survive the termination of this
                          Agreement. </li>
                        <li>A complete set of all data including information, and advices
                          provided
                          by the Consultant shall be made available to Company upon request
                          and/or
                          upon completion or termination of such Service, and such data shall
                          be
                          the sole property of Company. </li>
                        <li>This Agreement is governed by and shall be construed in accordance
                          with
                          laws of India. All disputes, controversies and claims arising out of
                          or
                          in connection with this Agreement shall be brought exclusively
                          before a
                          court of competent jurisdiction in Vadodara, Gujarat and both
                          parties
                          consents to the exclusive jurisdiction and venue of such court.</li>
                        <li>It is expressly acknowledged and agreed by both parties that neither
                          this Agreement nor any payment hereunder is in exchange for any
                          explicit
                          or implicit agreement or understanding that the Consultant will
                          purchase, order, prescribe, recommend or otherwise arrange for, or
                          provide preferential treatment for the Company’s products.
                          Furthermore,
                          the total payment for the Services represents the fair market value
                          for
                          the Services and has not been determined in any manner that takes
                          into
                          account the volume or value of any prescriptions, referrals or
                          business
                          between both parties.</li>
                        <li>This Agreement does not create any other business arrangement,
                          including
                          but not limited to any partnership, agency or joint venture, between
                          both parties. </li>
                        <li>This Agreement, together with annexure attached hereto, forms the
                          entire
                          agreement between both parties and supersedes all earlier
                          negotiations,
                          discussions, agreements and understandings between both parties. The
                          recitals above are incorporated herein by this reference and forms
                          an
                          integral part of this Agreement This Agreement shall not be changed,
                          modified, amended or supplemented except by a written instrument
                          duly
                          signed by both parties. Neither party shall have the authority to
                          make
                          any statements, representations or commitments of any kind, or to
                          take
                          any action, which shall be binding on the other party, without the
                          prior
                          written consent of such other party.</li>
                        <li>Each party will use its commercially reasonable efforts in good
                          faith to
                          adhere to its representations mentioned in this agreement.</li>
                        <li>This Agreement is personal and shall not be assigned by Consultant.
                          The
                          Consultant has the full power and authority to enter into this
                          Agreement
                          and to perform the Services under this Agreement.</li>
                        <li>Consultant shall indemnify and hold harmless Company from and
                          against
                          all claims, losses, damages, attorney fees, and/or cost & expenses
                          arising out of or in connection with breach of this Agreement and/or
                          misrepresentation, negligence, fraud or willful misconduct by the
                          Consultant or its representatives. </li>
                        <li>The Consultant consents to photographing and audio and video
                          recording
                          of the proceedings of Consultant’s oration/ speaking session forming
                          part of Services (collectively, “Recordings”) by Company or its
                          representatives, at Company’s discretion. Any intellectual property
                          generated from the Services provided by the Consultant, including
                          the
                          Recordings, shall vest exclusively with the Company, unless
                          otherwise
                          agreed to the contrary in writing by both parties. All such
                          intellectual
                          property and all materials and information made or developed by the
                          Consultant in connection with the Services provided will be sole
                          property of the Company and the Company shall have the right to use
                          such
                          materials and information in any manner whatsoever.</li>
                        <li>Company is committed to protect Consultant’s sensitive personal
                          information, which is why access to such information is being
                          limited to
                          authorized employees, agents, contractors, third parties, affiliates
                          or
                          others on a need to know basis only. </li>
                        <li>All notices required hereunder shall be given by (i) in writing and
                          personally delivered, (ii) sent by courier (charges prepaid) or
                          (iii)
                          registered mail with return receipt requested or speed post, and
                          addressed to the parties mentioned above, or at such other address
                          as
                          any party shall hereafter inform the other party by written notice
                          given
                          as aforesaid. All written notices so given shall be deemed effective
                          upon receipt.</li>
                        <li>This Agreement has been jointly prepared on the basis of the mutual
                          understanding of both parties and shall not be construed against
                          either
                          party by reason of such party’s being the drafter hereof or thereof.
                        </li>
                        <li>This Agreement is executed in counterparts, each of which shall be
                          deemed to be an original and both of which together shall constitute
                          one
                          and same agreement. An executed copy of this Agreement may be
                          delivered
                          by electronic mail in “portable document format” (“.pdf”), or by any
                          other electronic means intended to preserve the original graphic and
                          pictorial appearance of a document, shall constitute effective
                          execution
                          and delivery of this Agreement as to both parties and to be used in
                          lieu
                          of the original Agreement for all purposes. Both parties acknowledge
                          and
                          agree that this agreement may be executed by digital signature or
                          electronic signature, which shall be considered as an original
                          signature
                          for all purposes and shall have the same force and effect as an
                          original
                          signature.</li>
                      </ul>
                      <p class="m-2">Thanking you,</p>
                      <div class="col-md-12">
                        <div class="my-2">
                          <label class="container_check mt-3 text-left">I Accept
                            <input type="checkbox" class="checkbox-one" name="is_agree" v-model="isAgree"
                              :checked="isAgree">
                            <span class="checkmark"></span>
                          </label>
                          <span v-if="errors.is_agree" class="text-danger">{{ errors.is_agree }}</span>
                        </div>
                        <div class="row mx-auto justify-content-center">
                          <div class="col-md-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary w-100 btn-1">BACK</a>
                          </div>
                          <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 btn-2">SUBMIT</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <form v-if="response.survey?.agreement_type === 2" @submit.prevent="submitForm" method="POST"
              id="agreement">
              <input type="hidden" name="_token" :value="csrfToken" />
              <input type="hidden" name="doctor_id" :value="decodedDoctorId" />
              <div id="middle-wizard">
                <div class="step">
                  <div class="col-12 dash-div">
                    <div class="login-form domy-text">
                      <h4 class="text-center mb-5 heading"><b>CONSULTANCY AGREEMENT</b></h4>
                      <p class="my-2">
                        This Consultancy Agreement (<b>“Agreement”</b>) is made on
                        <b>{{ formattedAgreementDate }}</b>
                        by and between Alembic Pharmaceuticals Limited, a company incorporated
                        under
                        the laws of India and having its registered office at Alembic Road,
                        Vadodara
                        - 390 003, Gujarat, India (hereinafter referred to as <b>“Company”</b>),
                        <b>{{ response.doctor?.name }}</b> , having PAN#
                        <b>{{ response.doctor?.pan_number }}</b>,
                        Registration No. <b>{{ response.doctor?.registration_no }}</b> and
                        address at
                        <b>{{ response.doctor?.address }}</b> ,
                        <b>{{ response.doctor?.pin_code }}</b> India (hereinafter
                        referred to as <b>“Consultant”</b>)
                      </p>
                      <p><b>RECITALS </b> :</p>
                      <ul class="numeric-alpha m-3 text-align-justify">
                        <li><b>Whereas,</b> the Company is engaged in the business of the
                          development, manufacturing, sale and distribution of pharmaceutical,
                          medicinal and veterinary products in India and abroad and has its
                          own
                          research & development division.</li>
                        <li><b>Whereas,</b> Consultant represents that Consultant is an
                          independent
                          healthcare practitioner and has experience, data and updated
                          knowledge
                          on various practices, therapies and products and provides
                          consultancy
                          services to pharmaceutical companies which will enable them to
                          address
                          changes in product portfolio, current and ongoing approach to new
                          product development, product stabilization, efficacy of products,
                          etc.
                        </li>
                        <li><b>Whereas,</b> both parties appreciates that healthcare is a very
                          dynamic field which requires real time and nuanced understanding of
                          ailments, side effects, complications, responses etc. and these
                          needs to
                          be monitored on a regular basis to address new drug development,
                          getting
                          market intelligence, competitive advantage etc.</li>
                        <li><b>Whereas,</b> Company agrees to avail the Services (defined below)
                          from Consultant and Consultant agrees to provide the same to Company
                          in
                          accordance with terms and conditions of this Agreement.
                        </li>
                      </ul>
                      <p><b>NOW, THEREFORE, </b>both parties hereby agree as follows:</p>
                      <ul class="numeric-ul p-17">
                        <li>During the term of this Agreement, the Consultant shall provide
                          specific
                          services as more clearly defined in Annexure 1 (“Services”) and the
                          Company shall pay for the Services to the Consultant upon Services
                          rendered by the Consultant to the satisfaction of Company.</li>
                        <li>The Agreement shall be effective upon execution by both parties and
                          shall be valid till the completion of Services or earlier terminated
                          by
                          mutual consent of both parties in writing.</li>
                        <li>Consultant represents and warrants that (i) neither Consultant not
                          its
                          representatives are connected directly or indirectly to any
                          government
                          organization/agency/undertaking in any manner; and (ii) shall
                          perform
                          the Services in its individual capacity in compliance with all
                          applicable laws, rules, regulations and/or any guidelines, including
                          but
                          not limited to, National Medical Council Act, 2019, Indian Medical
                          Council (Professional Conduct, Etiquette and Ethics) Regulation,
                          2002
                          and Prevention of Corruption Act, 1988. Consultant shall notify in
                          advance to Company before entering into any kind of relationship
                          directly or indirectly with any government
                          organization/agency/undertaking during the validity of this
                          Agreement
                          and share copies of all permissions/NOC from the relevant government
                          organization/agency/undertaking required to perform Services under
                          this
                          Agreement by Consultant. The manner in which Consultant renders
                          Services
                          will be within Consultant’s sole control, maintaining his/her
                          autonomy,
                          integrity and discretion.</li>
                        <li>Consultant shall be responsible for obtaining, maintaining, and/or
                          displaying any and all authorizations, licenses, registrations or
                          other
                          permissions necessary to carry out operations or activities under
                          this
                          Agreement and also disclose and/or display its affiliations/Services
                          herein to maintain transparency. Failure to adhere to this clause 4
                          shall result in immediate termination of the Agreement without any
                          liability whatsoever.</li>
                        <li>For performance under this Agreement, Consultant shall be
                          compensated
                          Rs.<b>{{ response.doctor?.honorarium }}</b>/- (Rupees <b>
                            {{ response.doctor?.convertedAmount }}
                            Only</b>) plus taxes for the Services provided in Annexure 1.
                          All
                          fees shall be paid by Company after deduction of withholding tax as
                          per
                          applicable tax laws. Upon deduction of withholding tax, Company will
                          pay
                          the amounts of such taxes to the relevant governmental authority and
                          transmit to the Consultant the copy of official tax certificate or
                          other
                          evidence of such withholding tax.</li>
                        <li>Consultant shall comply with all the compliance requirements under
                          GST
                          laws and do all things necessary to enable Company to claim input
                          tax
                          credit in relation to GST laws payable under this Agreement or in
                          respect of Services provided under this Agreement. This shall
                          include,
                          but not limited to, (i) issuing invoices/debit notes/revised
                          invoices/credit notes as per the prescribed format, containing all
                          the
                          information as is required for us to avail input tax credit; (ii)
                          timely
                          submission of periodic statements/returns as per the GST laws within
                          specified time lines with complete and correct details as may be
                          prescribed; (iii) timely issuance of debit note within the
                          prescribed
                          time limit to enable us to take the credit; and (iv) timely payment
                          of
                          tax liability by utilization of admissible credit or through cash.
                          Further, any kind of Default in filing of GST return or payment of
                          GST
                          will result into holding of payment by Company till cure of such
                          default
                          in compliance with GST laws.</li>
                        <li>Each party acknowledges that this Agreement is non-exclusive. If
                          Company
                          performs itself or retains a third party to perform any services
                          Consultant will cooperate and coordinate with Company or such third
                          party as reasonably requested or required by such third parties to
                          perform their duties.</li>
                        <li>Consultant shall not dispense any patient data (personal information
                          of
                          patient) in contravention of Information Technology Act, 2000,
                          Health
                          Data Management Policy, 2020, Electronic Health Records (EHR)
                          Standards,
                          2016 and all other applicable laws prevailing from time to time
                          which
                          protect the privacy and interests of patients.</li>
                        <li>Company shall have the right to terminate this Agreement with
                          immediate
                          effect by notice in case of violation of this Agreement by
                          Consultant.
                          Notwithstanding anything mentioned in this Agreement. Company shall
                          have
                          the right to terminate this Agreement for convenience at any time
                          upon
                          giving prior written notice to Consultant. In case of such
                          termination,
                          Company will be only liable to pay for the Services rendered till
                          the
                          date of the termination. Upon termination of this Agreement for any
                          reason, it shall not affect or prejudice any rights which may have
                          accrued to the party prior to the expiration or termination of this
                          Agreement.</li>
                        <li>All information disclosed by Company to Consultant in connection
                          with
                          this Agreement and other related trade secrets, specifications,
                          technology, know-how and other confidential and proprietary
                          information
                          of Company (collectively, the “Confidential Information”) is and
                          will be
                          kept confidential by Consultant and not used by Consultant other
                          than in
                          connection with this Agreement, except to the extent such
                          Confidential
                          Information (i) becomes lawfully obtainable from other sources, (ii)
                          is
                          or becomes part of the public domain (other than act or omission of
                          the
                          Consultant),and/or (iii) is required by any Authority under any
                          applicable law or regulation, provided however that Consultant shall
                          consult with Company as to the contents of such disclosure. On
                          termination of this Agreement for any reason whatsoever, (i) all
                          Confidential Information and any documents received (including
                          copies
                          thereof) shall be returned by Consultant to Company without
                          retaining
                          any copy in any manner, and (ii) these confidentiality and non-use
                          obligations of Consultant shall survive the termination of this
                          Agreement. </li>
                        <li>A complete set of all data including information, and advices
                          provided
                          by the Consultant shall be made available to Company upon request
                          and/or
                          upon completion or termination of such Service, and such data shall
                          be
                          the sole property of Company. </li>
                        <li>This Agreement is governed by and shall be construed in accordance
                          with
                          laws of India. All disputes, controversies and claims arising out of
                          or
                          in connection with this Agreement shall be brought exclusively
                          before a
                          court of competent jurisdiction in Vadodara, Gujarat and both
                          parties
                          consents to the exclusive jurisdiction and venue of such court.</li>
                        <li>It is expressly acknowledged and agreed by both parties that neither
                          this Agreement nor any payment hereunder is in exchange for any
                          explicit
                          or implicit agreement or understanding that the Consultant will
                          purchase, order, prescribe, recommend or otherwise arrange for, or
                          provide preferential treatment for the Company’s products.
                          Furthermore,
                          the total payment for the Services represents the fair market value
                          for
                          the Services and has not been determined in any manner that takes
                          into
                          account the volume or value of any prescriptions, referrals or
                          business
                          between both parties.</li>
                        <li>This Agreement does not create any other business arrangement,
                          including
                          but not limited to any partnership, agency or joint venture, between
                          both parties. </li>
                        <li>This Agreement, together with annexure attached hereto, forms the
                          entire
                          agreement between both parties and supersedes all earlier
                          negotiations,
                          discussions, agreements and understandings between both parties. The
                          recitals above are incorporated herein by this reference and forms
                          an
                          integral part of this Agreement This Agreement shall not be changed,
                          modified, amended or supplemented except by a written instrument
                          duly
                          signed by both parties. Neither party shall have the authority to
                          make
                          any statements, representations or commitments of any kind, or to
                          take
                          any action, which shall be binding on the other party, without the
                          prior
                          written consent of such other party.</li>
                        <li>Each party will use its commercially reasonable efforts in good
                          faith to
                          adhere to its representations mentioned in this agreement.</li>
                        <li>This Agreement is personal and shall not be assigned by Consultant.
                          The
                          Consultant has the full power and authority to enter into this
                          Agreement
                          and to perform the Services under this Agreement.</li>
                        <li>Consultant shall indemnify and hold harmless Company from and
                          against
                          all claims, losses, damages, attorney fees, and/or cost & expenses
                          arising out of or in connection with breach of this Agreement and/or
                          misrepresentation, negligence, fraud or willful misconduct by the
                          Consultant or its representatives. </li>
                        <li>The Consultant consents to photographing and audio and video
                          recording
                          of the proceedings of Consultant’s oration/ speaking session forming
                          part of Services (collectively, “Recordings”) by Company or its
                          representatives, at Company’s discretion. Any intellectual property
                          generated from the Services provided by the Consultant, including
                          the
                          Recordings, shall vest exclusively with the Company, unless
                          otherwise
                          agreed to the contrary in writing by both parties. All such
                          intellectual
                          property and all materials and information made or developed by the
                          Consultant in connection with the Services provided will be sole
                          property of the Company and the Company shall have the right to use
                          such
                          materials and information in any manner whatsoever.</li>
                        <li>Company is committed to protect Consultant’s sensitive personal
                          information, which is why access to such information is being
                          limited to
                          authorized employees, agents, contractors, third parties, affiliates
                          or
                          others on a need to know basis only. </li>
                        <li>All notices required hereunder shall be given by (i) in writing and
                          personally delivered, (ii) sent by courier (charges prepaid) or
                          (iii)
                          registered mail with return receipt requested or speed post, and
                          addressed to the parties mentioned above, or at such other address
                          as
                          any party shall hereafter inform the other party by written notice
                          given
                          as aforesaid. All written notices so given shall be deemed effective
                          upon receipt.</li>
                        <li>This Agreement has been jointly prepared on the basis of the mutual
                          understanding of both parties and shall not be construed against
                          either
                          party by reason of such party’s being the drafter hereof or thereof.
                        </li>
                        <li>This Agreement is executed in counterparts, each of which shall be
                          deemed to be an original and both of which together shall constitute
                          one
                          and same agreement. An executed copy of this Agreement may be
                          delivered
                          by electronic mail in “portable document format” (“.pdf”), or by any
                          other electronic means intended to preserve the original graphic and
                          pictorial appearance of a document, shall constitute effective
                          execution
                          and delivery of this Agreement as to both parties and to be used in
                          lieu
                          of the original Agreement for all purposes. Both parties acknowledge
                          and
                          agree that this agreement may be executed by digital signature or
                          electronic signature, which shall be considered as an original
                          signature
                          for all purposes and shall have the same force and effect as an
                          original
                          signature.</li>
                      </ul>
                      <p class="m-2">Thanking you,</p>
                      <div class="col-md-12">
                        <div class="my-2">
                          <label class="container_check mt-3 text-left">I Accept
                            <input type="checkbox" class="checkbox-one" name="is_agree" v-model="isAgree"
                              :checked="isAgree">
                            <span class="checkmark"></span>
                          </label>
                          <span v-if="errors.is_agree" class="text-danger">{{ errors.is_agree }}</span>
                        </div>
                        <div class="row mx-auto justify-content-center">
                          <div class="col-md-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary w-100 btn-1">BACK</a>
                          </div>
                          <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 btn-2">SUBMIT</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
