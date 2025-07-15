<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;
use App\Models\TranslationHeader;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing translations
        Translation::truncate();
        
        // Ensure translation headers exist
        $this->ensureTranslationHeaders();
        
        // Define all translations
        $translations = [
            // English translations
            ['key' => 'account_name', 'locale' => 'en', 'value' => 'Account Name'],
            ['key' => 'account_type', 'locale' => 'en', 'value' => 'Account Type'],
            ['key' => 'date', 'locale' => 'en', 'value' => 'Date'],
            ['key' => 'satisfaction_level', 'locale' => 'en', 'value' => 'Satisfaction Level'],
            ['key' => 'required', 'locale' => 'en', 'value' => 'Required'],
            ['key' => 'optional', 'locale' => 'en', 'value' => 'Optional'],
            ['key' => 'recommendation', 'locale' => 'en', 'value' => 'Recommendation'],
            ['key' => 'recommendation_question', 'locale' => 'en', 'value' => 'How likely is it that you would recommend our company to a friend or colleague?'],
            ['key' => 'select_rating', 'locale' => 'en', 'value' => 'Select a rating'],
            ['key' => 'improvement_areas', 'locale' => 'en', 'value' => 'Areas for Improvement Suggestions'],
            ['key' => 'select_all_apply', 'locale' => 'en', 'value' => 'Select all that apply:'],
            ['key' => 'product_quality', 'locale' => 'en', 'value' => 'Product / Service Quality'],
            ['key' => 'delivery_logistics', 'locale' => 'en', 'value' => 'Delivery & Logistics'],
            ['key' => 'customer_service', 'locale' => 'en', 'value' => 'Sales & Customer Service'],
            ['key' => 'timeliness', 'locale' => 'en', 'value' => 'Timeliness'],
            ['key' => 'returns_handling', 'locale' => 'en', 'value' => 'Returns / BO Handling'],
            ['key' => 'others', 'locale' => 'en', 'value' => 'Others (please specify)'],
            ['key' => 'others_placeholder', 'locale' => 'en', 'value' => 'Please specify other areas for improvement...'],
            ['key' => 'submit_survey', 'locale' => 'en', 'value' => 'Submit Survey'],
            ['key' => 'thank_you', 'locale' => 'en', 'value' => 'THANK YOU!'],
            ['key' => 'thank_you_message', 'locale' => 'en', 'value' => 'WE APPRECIATE YOUR FEEDBACK!'],
            ['key' => 'feedback_helps', 'locale' => 'en', 'value' => 'Your input helps us serve you better.'],
            ['key' => 'view_response', 'locale' => 'en', 'value' => 'View Response'],
            ['key' => 'rating_poor', 'locale' => 'en', 'value' => '1 - Poor'],
            ['key' => 'rating_needs_improvement', 'locale' => 'en', 'value' => '2 - Needs Improvement'],
            ['key' => 'rating_satisfactory', 'locale' => 'en', 'value' => '3 - Satisfactory'],
            ['key' => 'rating_very_satisfactory', 'locale' => 'en', 'value' => '4 - Very Satisfactory'],
            ['key' => 'rating_excellent', 'locale' => 'en', 'value' => '5 - Excellent'],
            ['key' => 'validation_alert', 'locale' => 'en', 'value' => 'Please Fill In All Required Fields!'],
            
            // Consent form translations
            ['key' => 'consent_title', 'locale' => 'en', 'value' => 'Survey Consent Statement'],
            ['key' => 'consent_subtitle', 'locale' => 'en', 'value' => 'Customer Satisfaction Survey'],
            ['key' => 'consent_dear_customer', 'locale' => 'en', 'value' => 'Dear Valued Customer,'],
            ['key' => 'consent_intro', 'locale' => 'en', 'value' => 'We appreciate your participation in this customer satisfaction survey and your willingness to share your thoughts. Your insights will assist us in enhancing our services and client satisfaction.'],
            ['key' => 'consent_terms_intro', 'locale' => 'en', 'value' => 'By completing this survey, you acknowledge and agree to the following terms:'],
            ['key' => 'consent_question', 'locale' => 'en', 'value' => 'Please indicate your consent to proceed with the survey:'],
            ['key' => 'consent_accept', 'locale' => 'en', 'value' => 'I accept the terms and conditions and consent to participate'],
            ['key' => 'consent_decline', 'locale' => 'en', 'value' => 'I do not accept the terms and conditions'],
            ['key' => 'consent_continue', 'locale' => 'en', 'value' => 'Continue to Survey'],
            ['key' => 'consent_footer_note', 'locale' => 'en', 'value' => 'Thank you for your valuable time and feedback.'],
            ['key' => 'consent_voluntary', 'locale' => 'en', 'value' => 'Voluntary Participation'],
            ['key' => 'consent_voluntary_desc', 'locale' => 'en', 'value' => 'Your participation in this survey is entirely voluntary. You are not required to answer all questions, and you may withdraw at any time without penalty.'],
            ['key' => 'consent_purpose', 'locale' => 'en', 'value' => 'Purpose of Survey'],
            ['key' => 'consent_purpose_desc', 'locale' => 'en', 'value' => 'The information you provide will be used solely for the purpose of improving our services and understanding customer satisfaction levels.'],
            ['key' => 'consent_personal_info', 'locale' => 'en', 'value' => 'Personal Information'],
            ['key' => 'consent_personal_info_desc', 'locale' => 'en', 'value' => 'We will collect only the information necessary for this survey. Your personal details will be kept strictly confidential and will not be shared with third parties.'],
            ['key' => 'consent_confidentiality', 'locale' => 'en', 'value' => 'Confidentiality'],
            ['key' => 'consent_confidentiality_desc', 'locale' => 'en', 'value' => 'All responses will be kept confidential and anonymous. Individual responses will not be disclosed to anyone outside our organization.'],
            ['key' => 'consent_data_protection', 'locale' => 'en', 'value' => 'Data Protection'],
            ['key' => 'consent_data_protection_desc', 'locale' => 'en', 'value' => 'Your data will be stored securely and will be used only for the stated purposes. We comply with applicable data protection regulations.'],
            
            // Language selection translations
            ['key' => 'language_selection_title', 'locale' => 'en', 'value' => 'Language Selection'],
            ['key' => 'language_selection_subtitle', 'locale' => 'en', 'value' => 'Please select your preferred language for the survey'],
            ['key' => 'language_selection_continue', 'locale' => 'en', 'value' => 'Continue'],
            ['key' => 'language_selection_note', 'locale' => 'en', 'value' => 'You can change the language at any time during the survey.'],
            ['key' => 'language_english', 'locale' => 'en', 'value' => 'English'],
            ['key' => 'language_tagalog', 'locale' => 'en', 'value' => 'Tagalog'],
            ['key' => 'language_cebuano', 'locale' => 'en', 'value' => 'Cebuano'],
            
            // Improvement details translations
            ['key' => 'improvement_details.product_quality.availability', 'locale' => 'en', 'value' => 'We hope products are always available. Some items are often out of stock.'],
            ['key' => 'improvement_details.product_quality.expiration', 'locale' => 'en', 'value' => 'Please monitor product expiration dates more carefully. We sometimes receive items that are near expiry.'],
            ['key' => 'improvement_details.product_quality.damage', 'locale' => 'en', 'value' => 'Some products arrive with dents, leaks, or damaged packaging. Kindly ensure all items are in good condition.'],
            ['key' => 'improvement_details.delivery_logistics.on_time', 'locale' => 'en', 'value' => 'We\'d appreciate it if deliveries consistently arrive on time, as promised.'],
            ['key' => 'improvement_details.delivery_logistics.missing_items', 'locale' => 'en', 'value' => 'There have been a few instances of missing items in our deliveries. Please double-check orders for completeness.'],
            ['key' => 'improvement_details.customer_service.response_time', 'locale' => 'en', 'value' => 'It would be helpful if our concerns or follow-ups were responded to more quickly.'],
            ['key' => 'improvement_details.customer_service.communication', 'locale' => 'en', 'value' => 'We appreciate clear communication. Kindly ensure that all interactions remain polite and professional.'],
            ['key' => 'improvement_details.timeliness.schedule', 'locale' => 'en', 'value' => 'Please try to follow the agreed delivery or visit schedule to avoid disruptions in our store operations.'],
            ['key' => 'improvement_details.returns_handling.return_process', 'locale' => 'en', 'value' => 'I hope the return process can be made quicker and more convenient.'],
            ['key' => 'improvement_details.returns_handling.bo_coordination', 'locale' => 'en', 'value' => 'Please improve coordination when it comes to picking up bad order items.'],
            
            // Tagalog translations
            ['key' => 'account_name', 'locale' => 'tl', 'value' => 'Pangalan ng Account'],
            ['key' => 'account_type', 'locale' => 'tl', 'value' => 'Uri ng Account'],
            ['key' => 'date', 'locale' => 'tl', 'value' => 'Petsa'],
            ['key' => 'satisfaction_level', 'locale' => 'tl', 'value' => 'Antas ng Kasiyahan'],
            ['key' => 'required', 'locale' => 'tl', 'value' => 'Kailangan'],
            ['key' => 'optional', 'locale' => 'tl', 'value' => 'Opsyonal'],
            ['key' => 'recommendation', 'locale' => 'tl', 'value' => 'Rekomendasyon'],
            ['key' => 'recommendation_question', 'locale' => 'tl', 'value' => 'Gaano ka malamang na irerekumenda mo ang aming kumpanya sa iyong kaibigan o kasamahan?'],
            ['key' => 'select_rating', 'locale' => 'tl', 'value' => 'Pumili ng rating'],
            ['key' => 'improvement_areas', 'locale' => 'tl', 'value' => 'Mga Lugar para sa Pagpapabuti ng mga Mungkahi'],
            ['key' => 'select_all_apply', 'locale' => 'tl', 'value' => 'Piliin lahat na naaangkop:'],
            ['key' => 'product_quality', 'locale' => 'tl', 'value' => 'Kalidad ng Produkto / Serbisyo'],
            ['key' => 'delivery_logistics', 'locale' => 'tl', 'value' => 'Paghahatid at Logistics'],
            ['key' => 'customer_service', 'locale' => 'tl', 'value' => 'Benta at Customer Service'],
            ['key' => 'timeliness', 'locale' => 'tl', 'value' => 'Pagkamaagap'],
            ['key' => 'returns_handling', 'locale' => 'tl', 'value' => 'Pag-handle ng Returns / BO'],
            ['key' => 'others', 'locale' => 'tl', 'value' => 'Iba pa (pakitukoy)'],
            ['key' => 'others_placeholder', 'locale' => 'tl', 'value' => 'Pakitukoy ang iba pang mga lugar para sa pagpapabuti...'],
            ['key' => 'submit_survey', 'locale' => 'tl', 'value' => 'Isumite ang Survey'],
            ['key' => 'thank_you', 'locale' => 'tl', 'value' => 'SALAMAT!'],
            ['key' => 'thank_you_message', 'locale' => 'tl', 'value' => 'PINASASALAMATAN NAMIN ANG INYONG FEEDBACK!'],
            ['key' => 'feedback_helps', 'locale' => 'tl', 'value' => 'Ang inyong input ay tumutulong sa amin na magbigay ng mas magandang serbisyo.'],
            ['key' => 'view_response', 'locale' => 'tl', 'value' => 'Tingnan ang Sagot'],
            ['key' => 'rating_poor', 'locale' => 'tl', 'value' => '1 - Mahina'],
            ['key' => 'rating_needs_improvement', 'locale' => 'tl', 'value' => '2 - Kailangan ng Pagpapabuti'],
            ['key' => 'rating_satisfactory', 'locale' => 'tl', 'value' => '3 - Kasiya-siya'],
            ['key' => 'rating_very_satisfactory', 'locale' => 'tl', 'value' => '4 - Napakasiya-siya'],
            ['key' => 'rating_excellent', 'locale' => 'tl', 'value' => '5 - Napakagaling'],
            ['key' => 'validation_alert', 'locale' => 'tl', 'value' => 'Pakipunan ang lahat ng kailangang mga patlang!'],
            
            // Tagalog consent form translations
            ['key' => 'consent_title', 'locale' => 'tl', 'value' => 'Pahayag ng Pagpapahintulot sa Survey'],
            ['key' => 'consent_subtitle', 'locale' => 'tl', 'value' => 'Survey ng Kasiyahan ng Customer'],
            ['key' => 'consent_dear_customer', 'locale' => 'tl', 'value' => 'Minamahal na Customer,'],
            ['key' => 'consent_intro', 'locale' => 'tl', 'value' => 'Pinasasalamatan namin ang inyong partisipasyon sa customer satisfaction survey na ito at ang inyong pagkahandang magbahagi ng inyong mga kaisipan. Ang inyong mga puna ay makakatulong sa amin na mapabuti ang aming mga serbisyo at kasiyahan ng mga kliyente.'],
            ['key' => 'consent_terms_intro', 'locale' => 'tl', 'value' => 'Sa pamamagitan ng pagkumpleto ng survey na ito, kinikilala at sumasang-ayon kayo sa mga sumusunod na tuntunin:'],
            ['key' => 'consent_question', 'locale' => 'tl', 'value' => 'Pakiipahayag ang inyong pahintulot na magpatuloy sa survey:'],
            ['key' => 'consent_accept', 'locale' => 'tl', 'value' => 'Tinatanggap ko ang mga tuntunin at kondisyon at pumapayag na lumahok'],
            ['key' => 'consent_decline', 'locale' => 'tl', 'value' => 'Hindi ko tinatanggap ang mga tuntunin at kondisyon'],
            ['key' => 'consent_continue', 'locale' => 'tl', 'value' => 'Magpatuloy sa Survey'],
            ['key' => 'consent_footer_note', 'locale' => 'tl', 'value' => 'Salamat sa inyong mahalalagang oras at feedback.'],
            ['key' => 'consent_voluntary', 'locale' => 'tl', 'value' => 'Kusang Paglahok'],
            ['key' => 'consent_voluntary_desc', 'locale' => 'tl', 'value' => 'Ang inyong paglahok sa survey na ito ay lubos na kusang-loob. Hindi kayo obligadong sagutin ang lahat ng mga katanungan, at maaari kayong umalis anumang oras nang walang parusa.'],
            ['key' => 'consent_purpose', 'locale' => 'tl', 'value' => 'Layunin ng Survey'],
            ['key' => 'consent_purpose_desc', 'locale' => 'tl', 'value' => 'Ang impormasyong inyong ibibigay ay gagamitin lamang para sa layuning mapabuti ang aming mga serbisyo at maunawaan ang mga antas ng kasiyahan ng customer.'],
            ['key' => 'consent_personal_info', 'locale' => 'tl', 'value' => 'Personal na Impormasyon'],
            ['key' => 'consent_personal_info_desc', 'locale' => 'tl', 'value' => 'Kokolektahin namin lamang ang impormasyong kinakailangan para sa survey na ito. Ang inyong mga personal na detalye ay magiging tuwiran at hindi ibabahagi sa ibang mga partido.'],
            ['key' => 'consent_confidentiality', 'locale' => 'tl', 'value' => 'Pagiging Kumpidensyal'],
            ['key' => 'consent_confidentiality_desc', 'locale' => 'tl', 'value' => 'Lahat ng mga sagot ay mananatiling kumpidensyal at anonymous. Ang mga indibidwal na sagot ay hindi ibubunyag sa sinuman sa labas ng aming organisasyon.'],
            ['key' => 'consent_data_protection', 'locale' => 'tl', 'value' => 'Proteksyon ng Data'],
            ['key' => 'consent_data_protection_desc', 'locale' => 'tl', 'value' => 'Ang inyong data ay itatago nang secure at gagamitin lamang para sa mga nabanggit na layunin. Sumusunod kami sa mga naaangkop na regulasyon sa proteksyon ng data.'],
            
            // Tagalog language selection translations
            ['key' => 'language_selection_title', 'locale' => 'tl', 'value' => 'Pagpili ng Wika'],
            ['key' => 'language_selection_subtitle', 'locale' => 'tl', 'value' => 'Pakipili ang inyong gustong wika para sa survey'],
            ['key' => 'language_selection_continue', 'locale' => 'tl', 'value' => 'Magpatuloy'],
            ['key' => 'language_selection_note', 'locale' => 'tl', 'value' => 'Maaari ninyong baguhin ang wika anumang oras sa survey.'],
            ['key' => 'language_english', 'locale' => 'tl', 'value' => 'English'],
            ['key' => 'language_tagalog', 'locale' => 'tl', 'value' => 'Tagalog'],
            ['key' => 'language_cebuano', 'locale' => 'tl', 'value' => 'Cebuano'],
            
            // Tagalog improvement details translations
            ['key' => 'improvement_details.product_quality.availability', 'locale' => 'tl', 'value' => 'Umaasa kaming laging available ang mga produkto. Madalas na out of stock ang ilang items.'],
            ['key' => 'improvement_details.product_quality.expiration', 'locale' => 'tl', 'value' => 'Pakibantayan nang mas maingat ang mga expiration date ng produkto. Minsan nakakakuha kami ng mga item na malapit nang mag-expire.'],
            ['key' => 'improvement_details.product_quality.damage', 'locale' => 'tl', 'value' => 'Ang ilang produkto ay dumarating na may mga dents, leaks, o sirang packaging. Pakitiyak na lahat ng items ay nasa magandang kondisyon.'],
            ['key' => 'improvement_details.delivery_logistics.on_time', 'locale' => 'tl', 'value' => 'Maappreciate namin kung palagi na lang on time ang mga delivery, gaya ng pangako.'],
            ['key' => 'improvement_details.delivery_logistics.missing_items', 'locale' => 'tl', 'value' => 'May ilang pagkakataon na may mga missing items sa aming deliveries. Pakidouble-check ang orders para sa completeness.'],
            ['key' => 'improvement_details.customer_service.response_time', 'locale' => 'tl', 'value' => 'Makakatulong kung mas mabilis na matutugon ang aming mga concerns o follow-ups.'],
            ['key' => 'improvement_details.customer_service.communication', 'locale' => 'tl', 'value' => 'Appreciate namin ang clear communication. Pakitiyak na lahat ng interactions ay nananatiling polite at professional.'],
            ['key' => 'improvement_details.timeliness.schedule', 'locale' => 'tl', 'value' => 'Pakisubukan na sundin ang agreed delivery o visit schedule para maiwasan ang disruptions sa aming store operations.'],
            ['key' => 'improvement_details.returns_handling.return_process', 'locale' => 'tl', 'value' => 'Sana mas mabilis at mas convenient ang return process.'],
            ['key' => 'improvement_details.returns_handling.bo_coordination', 'locale' => 'tl', 'value' => 'Pakiimprove ang coordination pagdating sa pagkuha ng bad order items.'],
            
            // Cebuano translations
            ['key' => 'account_name', 'locale' => 'ceb', 'value' => 'Ngalan sa Account'],
            ['key' => 'account_type', 'locale' => 'ceb', 'value' => 'Type sa Account'],
            ['key' => 'date', 'locale' => 'ceb', 'value' => 'Petsa'],
            ['key' => 'satisfaction_level', 'locale' => 'ceb', 'value' => 'Lebel sa Katagbawan'],
            ['key' => 'required', 'locale' => 'ceb', 'value' => 'Gikinahanglan'],
            ['key' => 'optional', 'locale' => 'ceb', 'value' => 'Opsyonal'],
            ['key' => 'recommendation', 'locale' => 'ceb', 'value' => 'Rekomendasyon'],
            ['key' => 'recommendation_question', 'locale' => 'ceb', 'value' => 'Unsa ka posible nga imong irekomenda ang among kompanya sa imong higala o kauban?'],
            ['key' => 'select_rating', 'locale' => 'ceb', 'value' => 'Pagpili og rating'],
            ['key' => 'improvement_areas', 'locale' => 'ceb', 'value' => 'Mga Lugar para sa Pagpauswag nga mga Sugyot'],
            ['key' => 'select_all_apply', 'locale' => 'ceb', 'value' => 'Pagpili sa tanan nga magamit:'],
            ['key' => 'product_quality', 'locale' => 'ceb', 'value' => 'Kalidad sa Produkto / Serbisyo'],
            ['key' => 'delivery_logistics', 'locale' => 'ceb', 'value' => 'Pagdala ug Logistics'],
            ['key' => 'customer_service', 'locale' => 'ceb', 'value' => 'Baligya ug Customer Service'],
            ['key' => 'timeliness', 'locale' => 'ceb', 'value' => 'Pagkamatuod sa Oras'],
            ['key' => 'returns_handling', 'locale' => 'ceb', 'value' => 'Pagdumala sa Returns / BO'],
            ['key' => 'others', 'locale' => 'ceb', 'value' => 'Uban pa (palihug tukya)'],
            ['key' => 'others_placeholder', 'locale' => 'ceb', 'value' => 'Palihug tukya ang ubang lugar para sa pagpauswag...'],
            ['key' => 'submit_survey', 'locale' => 'ceb', 'value' => 'Isumite ang Survey'],
            ['key' => 'thank_you', 'locale' => 'ceb', 'value' => 'SALAMAT!'],
            ['key' => 'thank_you_message', 'locale' => 'ceb', 'value' => 'GIPASALAMATAN NAMO ANG IMONG FEEDBACK!'],
            ['key' => 'feedback_helps', 'locale' => 'ceb', 'value' => 'Ang imong input nagtabang kanamo nga makahatag og mas maayong serbisyo.'],
            ['key' => 'view_response', 'locale' => 'ceb', 'value' => 'Tan-awa ang Tubag'],
            ['key' => 'rating_poor', 'locale' => 'ceb', 'value' => '1 - Dili Maayo'],
            ['key' => 'rating_needs_improvement', 'locale' => 'ceb', 'value' => '2 - Kinahanglan og Pagpauswag'],
            ['key' => 'rating_satisfactory', 'locale' => 'ceb', 'value' => '3 - Maayo'],
            ['key' => 'rating_very_satisfactory', 'locale' => 'ceb', 'value' => '4 - Maayo Kaayo'],
            ['key' => 'rating_excellent', 'locale' => 'ceb', 'value' => '5 - Perpekto'],
            ['key' => 'validation_alert', 'locale' => 'ceb', 'value' => 'Palihug pun-a ang tanan nga gikinahanglan nga mga patlang!'],
            
            // Cebuano consent form translations
            ['key' => 'consent_title', 'locale' => 'ceb', 'value' => 'Pahayag sa Pagpahintulot sa Survey'],
            ['key' => 'consent_subtitle', 'locale' => 'ceb', 'value' => 'Survey sa Katagbawan sa Customer'],
            ['key' => 'consent_dear_customer', 'locale' => 'ceb', 'value' => 'Minahal nga Customer,'],
            ['key' => 'consent_intro', 'locale' => 'ceb', 'value' => 'Gipasalamatan namo ang imong partisipasyon niini nga customer satisfaction survey ug ang imong kaandam sa pagpaambit sa imong mga hunahuna. Ang imong mga panabut motabang kanamo sa pagpauswag sa among mga serbisyo ug kasagaran sa mga kliyente.'],
            ['key' => 'consent_terms_intro', 'locale' => 'ceb', 'value' => 'Pinaagi sa pagtapos niini nga survey, giila ug giuyon nimo ang mosunod nga mga termino:'],
            ['key' => 'consent_question', 'locale' => 'ceb', 'value' => 'Palihug ipakita ang imong consent sa pagpadayon sa survey:'],
            ['key' => 'consent_accept', 'locale' => 'ceb', 'value' => 'Gidawat nako ang mga termino ug kondisyon ug gusto kong magpadayon sa survey'],
            ['key' => 'consent_decline', 'locale' => 'ceb', 'value' => 'Wala nako gidawat ang mga termino ug kondisyon'],
            ['key' => 'consent_continue', 'locale' => 'ceb', 'value' => 'Padayon sa Survey'],
            ['key' => 'consent_footer_note', 'locale' => 'ceb', 'value' => 'Salamat sa imong bililhong oras ug feedback.'],
            ['key' => 'consent_voluntary', 'locale' => 'ceb', 'value' => 'Boluntaryong Pag-apil'],
            ['key' => 'consent_voluntary_desc', 'locale' => 'ceb', 'value' => 'Ang imong pag-apil niini nga survey hingpit nga boluntaryo. Dili ka obligado sa pagtubag sa tanang mga pangutana, ug mahimo ka mobiya bisan unsa nga oras nga walay silot.'],
            ['key' => 'consent_purpose', 'locale' => 'ceb', 'value' => 'Katuyoan sa Survey'],
            ['key' => 'consent_purpose_desc', 'locale' => 'ceb', 'value' => 'Ang impormasyon nga imong ihatag gamiton lamang para sa katuyoan sa pagpauswag sa among mga serbisyo ug pagsabot sa mga lebel sa kasagaran sa customer.'],
            ['key' => 'consent_personal_info', 'locale' => 'ceb', 'value' => 'Personal nga Impormasyon'],
            ['key' => 'consent_personal_info_desc', 'locale' => 'ceb', 'value' => 'Kolektahon namo lamang ang impormasyon nga gikinahanglan para niini nga survey. Ang imong mga personal nga detalye magpabilin nga tago-tago ug dili ipaambit sa laing mga partido.'],
            ['key' => 'consent_confidentiality', 'locale' => 'ceb', 'value' => 'Pagiging Kompidensyal'],
            ['key' => 'consent_confidentiality_desc', 'locale' => 'ceb', 'value' => 'Ang tanang mga tubag magpabilin nga kompidensyal ug anonymous. Ang mga indibidwal nga tubag dili ibutyag sa bisan kinsa sa gawas sa among organisasyon.'],
            ['key' => 'consent_data_protection', 'locale' => 'ceb', 'value' => 'Proteksyon sa Data'],
            ['key' => 'consent_data_protection_desc', 'locale' => 'ceb', 'value' => 'Ang imong data tipigan nga luwas ug gamiton lamang para sa mga gitaho nga mga katuyoan. Mosunod kami sa mga magamit nga regulasyon sa proteksyon sa data.'],
            
            // Cebuano language selection translations
            ['key' => 'language_selection_title', 'locale' => 'ceb', 'value' => 'Pagpili og Pinulongan'],
            ['key' => 'language_selection_subtitle', 'locale' => 'ceb', 'value' => 'Palihug pilia ang imong gusto nga pinulongan para sa survey'],
            ['key' => 'language_selection_continue', 'locale' => 'ceb', 'value' => 'Padayon'],
            ['key' => 'language_selection_note', 'locale' => 'ceb', 'value' => 'Mahimo nimong usbon ang pinulongan bisan unsa nga oras atol sa survey.'],
            ['key' => 'language_english', 'locale' => 'ceb', 'value' => 'English'],
            ['key' => 'language_tagalog', 'locale' => 'ceb', 'value' => 'Tagalog'],
            ['key' => 'language_cebuano', 'locale' => 'ceb', 'value' => 'Cebuano'],
            
            // Cebuano improvement details translations
            ['key' => 'improvement_details.product_quality.availability', 'locale' => 'ceb', 'value' => 'Naglaum kami nga ang mga produkto kanunay makuha. Ang pipila ka mga butang kanunay nga walay stock.'],
            ['key' => 'improvement_details.product_quality.expiration', 'locale' => 'ceb', 'value' => 'Palihug bantayi ang mga petsa sa pagkaexpire sa produkto nga mas maampingon. Usahay makadawat kami og mga butang nga hapit nang ma-expire.'],
            ['key' => 'improvement_details.product_quality.damage', 'locale' => 'ceb', 'value' => 'Ang pipila ka mga produkto moabot nga may mga dako, pagkatubo, o guba nga pagkabalot. Palihug siguruha nga ang tanan nga mga butang naa sa maayong kondisyon.'],
            ['key' => 'improvement_details.delivery_logistics.on_time', 'locale' => 'ceb', 'value' => 'Mapasalamaton namo kung ang mga pagdala kanunay nga moabot sa hustong oras, sumala sa gisaad.'],
            ['key' => 'improvement_details.delivery_logistics.missing_items', 'locale' => 'ceb', 'value' => 'Adunay pipila ka mga higayon sa nawala nga mga butang sa among mga pagdala. Palihug susihon og maayo ang mga order para sa pagkakompleto.'],
            ['key' => 'improvement_details.customer_service.response_time', 'locale' => 'ceb', 'value' => 'Makatabang kung ang among mga kabalaka o mga follow-up matubag nga mas kusog.'],
            ['key' => 'improvement_details.customer_service.communication', 'locale' => 'ceb', 'value' => 'Gipasalamatan namo ang klaro nga komunikasyon. Palihug siguruha nga ang tanan nga mga pakig-uban magpadayon nga mabination ug propesyonal.'],
            ['key' => 'improvement_details.timeliness.schedule', 'locale' => 'ceb', 'value' => 'Palihug sulayi nga sundon ang nahisgutang iskhedyul sa pagdala o pagbisita aron malikayan ang mga pagkabalda sa among operasyon sa tindahan.'],
            ['key' => 'improvement_details.returns_handling.return_process', 'locale' => 'ceb', 'value' => 'Naglaum ko nga ang proseso sa pagbalik mahimong mas paspas ug mas sayon.'],
            ['key' => 'improvement_details.returns_handling.bo_coordination', 'locale' => 'ceb', 'value' => 'Palihug pauswaga ang koordinasyon kung bahin sa pagkuha sa mga dautang order nga mga butang.'],
        ];
        
        // Get translation header IDs
        $enHeader = TranslationHeader::where('locale', 'en')->first();
        $tlHeader = TranslationHeader::where('locale', 'tl')->first();
        $cebHeader = TranslationHeader::where('locale', 'ceb')->first();
        
        // Convert translations to use translation_header_id
        $translationsToInsert = [];
        foreach ($translations as $translation) {
            $headerId = null;
            switch ($translation['locale']) {
                case 'en':
                    $headerId = $enHeader->id;
                    break;
                case 'tl':
                    $headerId = $tlHeader->id;
                    break;
                case 'ceb':
                    $headerId = $cebHeader->id;
                    break;
            }
            
            if ($headerId) {
                $translationsToInsert[] = [
                    'key' => $translation['key'],
                    'translation_header_id' => $headerId,
                    'value' => $translation['value'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        // Batch insert translations
        Translation::insert($translationsToInsert);
        
        $this->command->info('Translation seeder completed successfully! Added ' . count($translationsToInsert) . ' translations.');
    }
    
    /**
     * Ensure translation headers exist
     */
    private function ensureTranslationHeaders(): void
    {
        $headers = [
            ['name' => 'English', 'locale' => 'en', 'is_active' => true],
            ['name' => 'Tagalog', 'locale' => 'tl', 'is_active' => true],
            ['name' => 'Cebuano', 'locale' => 'ceb', 'is_active' => true]
        ];
        
        foreach ($headers as $header) {
            TranslationHeader::updateOrCreate(
                ['locale' => $header['locale']],
                $header
            );
        }
    }
}
