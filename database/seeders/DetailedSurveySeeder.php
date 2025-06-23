<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use App\Models\Sbu;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DetailedSurveySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Get all SBUs and Sites
        $sbus = Sbu::all();
        $sites = Site::all();
        $admins = Admin::where('status', 1)->get();
        
        if ($sbus->isEmpty() || $sites->isEmpty() || $admins->isEmpty()) {
            $this->command->error('Please ensure SBUs, Sites, and Admins exist before running this seeder');
            return;
        }
        
        $this->command->info('Creating detailed surveys with comprehensive questions...');
        
        // Employee Satisfaction Survey
        $this->createEmployeeSatisfactionSurvey($faker, $sbus, $sites, $admins);
        
        // Customer Experience Survey
        $this->createCustomerExperienceSurvey($faker, $sbus, $sites, $admins);
        
        // Training Effectiveness Survey
        $this->createTrainingEffectivenessSurvey($faker, $sbus, $sites, $admins);
        
        // Workplace Safety Survey
        $this->createWorkplaceSafetySurvey($faker, $sbus, $sites, $admins);
        
        // Performance Review Survey
        $this->createPerformanceReviewSurvey($faker, $sbus, $sites, $admins);
        
        $this->command->info('Detailed survey seeding completed!');
    }
    
    private function createEmployeeSatisfactionSurvey($faker, $sbus, $sites, $admins)
    {
        $survey = Survey::create([
            'title' => 'Employee Satisfaction Survey - Q4 2024',
            'admin_id' => $admins->random()->id,
            'is_active' => true,
            'total_questions' => 0,
            'logo' => 'company_logo.png',
        ]);
        
        // Assign to random SBUs and sites
        $randomSbus = $sbus->random($faker->numberBetween(1, 2));
        $survey->sbus()->attach($randomSbus->pluck('id'));
        
        $surveySiteIds = [];
        foreach ($randomSbus as $sbu) {
            $sbuSites = $sites->where('sbu_id', $sbu->id);
            $randomSbuSites = $sbuSites->random($faker->numberBetween(2, 4));
            $surveySiteIds = array_merge($surveySiteIds, $randomSbuSites->pluck('id')->toArray());
        }
        $survey->sites()->attach(array_unique($surveySiteIds));
        
        // Create comprehensive questions
        $questions = [
            ['text' => 'What is your current position/role?', 'type' => 'text', 'required' => true],
            ['text' => 'How long have you been with the company?', 'type' => 'select', 'required' => true],
            ['text' => 'Overall, how satisfied are you with your job?', 'type' => 'radio', 'required' => true],
            ['text' => 'How satisfied are you with your immediate supervisor?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate the communication within your team?', 'type' => 'radio', 'required' => true],
            ['text' => 'Do you feel your workload is manageable?', 'type' => 'radio', 'required' => true],
            ['text' => 'How satisfied are you with your compensation?', 'type' => 'radio', 'required' => true],
            ['text' => 'How satisfied are you with the benefits package?', 'type' => 'radio', 'required' => true],
            ['text' => 'Do you feel valued and recognized for your contributions?', 'type' => 'radio', 'required' => true],
            ['text' => 'How satisfied are you with career advancement opportunities?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate the work-life balance?', 'type' => 'radio', 'required' => true],
            ['text' => 'How satisfied are you with the training and development opportunities?', 'type' => 'radio', 'required' => true],
            ['text' => 'Which areas would you like to see improved? (Select all that apply)', 'type' => 'checkbox', 'required' => false],
            ['text' => 'What do you like most about working here?', 'type' => 'textarea', 'required' => false],
            ['text' => 'What suggestions do you have for improvement?', 'type' => 'textarea', 'required' => false],
            ['text' => 'Would you recommend this company as a good place to work?', 'type' => 'radio', 'required' => true],
        ];
        
        foreach ($questions as $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'text' => $questionData['text'],
                'type' => $questionData['type'],
                'required' => $questionData['required'],
            ]);
        }
    }
    
    private function createCustomerExperienceSurvey($faker, $sbus, $sites, $admins)
    {
        $survey = Survey::create([
            'title' => 'Customer Experience Feedback Survey',
            'admin_id' => $admins->random()->id,
            'is_active' => true,
            'total_questions' => 0,
            'logo' => 'customer_logo.png',
        ]);
        
        // Assign to random SBUs and sites
        $randomSbus = $sbus->random($faker->numberBetween(1, 2));
        $survey->sbus()->attach($randomSbus->pluck('id'));
        
        $surveySiteIds = [];
        foreach ($randomSbus as $sbu) {
            $sbuSites = $sites->where('sbu_id', $sbu->id);
            $randomSbuSites = $sbuSites->random($faker->numberBetween(3, 5));
            $surveySiteIds = array_merge($surveySiteIds, $randomSbuSites->pluck('id')->toArray());
        }
        $survey->sites()->attach(array_unique($surveySiteIds));
        
        $questions = [
            ['text' => 'How did you first learn about our services?', 'type' => 'select', 'required' => true],
            ['text' => 'How long have you been using our services?', 'type' => 'select', 'required' => true],
            ['text' => 'How would you rate your overall experience with our service?', 'type' => 'radio', 'required' => true],
            ['text' => 'How satisfied are you with the quality of our products/services?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate our customer service team?', 'type' => 'radio', 'required' => true],
            ['text' => 'How easy is it to get support when you need it?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate the response time to your inquiries?', 'type' => 'radio', 'required' => true],
            ['text' => 'How likely are you to recommend our services to others?', 'type' => 'radio', 'required' => true],
            ['text' => 'Which of our services do you use most frequently?', 'type' => 'checkbox', 'required' => false],
            ['text' => 'What aspects of our service do you value most?', 'type' => 'checkbox', 'required' => false],
            ['text' => 'What areas do you think we could improve?', 'type' => 'textarea', 'required' => false],
            ['text' => 'Any additional comments or suggestions?', 'type' => 'textarea', 'required' => false],
        ];
        
        foreach ($questions as $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'text' => $questionData['text'],
                'type' => $questionData['type'],
                'required' => $questionData['required'],
            ]);
        }
    }
    
    private function createTrainingEffectivenessSurvey($faker, $sbus, $sites, $admins)
    {
        $survey = Survey::create([
            'title' => 'Training Program Effectiveness Assessment',
            'admin_id' => $admins->random()->id,
            'is_active' => true,
            'total_questions' => 0,
            'logo' => 'training_logo.png',
        ]);
        
        // Assign to random SBUs and sites
        $randomSbus = $sbus->random($faker->numberBetween(1, 2));
        $survey->sbus()->attach($randomSbus->pluck('id'));
        
        $surveySiteIds = [];
        foreach ($randomSbus as $sbu) {
            $sbuSites = $sites->where('sbu_id', $sbu->id);
            $randomSbuSites = $sbuSites->random($faker->numberBetween(2, 4));
            $surveySiteIds = array_merge($surveySiteIds, $randomSbuSites->pluck('id')->toArray());
        }
        $survey->sites()->attach(array_unique($surveySiteIds));
        
        $questions = [
            ['text' => 'Which training program are you evaluating?', 'type' => 'select', 'required' => true],
            ['text' => 'When did you complete this training?', 'type' => 'date', 'required' => true],
            ['text' => 'How would you rate the overall quality of the training?', 'type' => 'radio', 'required' => true],
            ['text' => 'How relevant was the training content to your job?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate the trainer\'s knowledge and expertise?', 'type' => 'radio', 'required' => true],
            ['text' => 'How engaging was the training delivery?', 'type' => 'radio', 'required' => true],
            ['text' => 'How clear were the training objectives?', 'type' => 'radio', 'required' => true],
            ['text' => 'How well were the training materials organized?', 'type' => 'radio', 'required' => true],
            ['text' => 'How adequate was the training duration?', 'type' => 'radio', 'required' => true],
            ['text' => 'How confident do you feel applying what you learned?', 'type' => 'radio', 'required' => true],
            ['text' => 'Which training methods were most effective for you?', 'type' => 'checkbox', 'required' => false],
            ['text' => 'What topics would you like covered in future training?', 'type' => 'textarea', 'required' => false],
            ['text' => 'Any suggestions for improving the training program?', 'type' => 'textarea', 'required' => false],
        ];
        
        foreach ($questions as $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'text' => $questionData['text'],
                'type' => $questionData['type'],
                'required' => $questionData['required'],
            ]);
        }
    }
    
    private function createWorkplaceSafetySurvey($faker, $sbus, $sites, $admins)
    {
        $survey = Survey::create([
            'title' => 'Workplace Safety and Health Assessment',
            'admin_id' => $admins->random()->id,
            'is_active' => true,
            'total_questions' => 0,
            'logo' => 'safety_logo.png',
        ]);
        
        // Assign to random SBUs and sites
        $randomSbus = $sbus->random($faker->numberBetween(1, 2));
        $survey->sbus()->attach($randomSbus->pluck('id'));
        
        $surveySiteIds = [];
        foreach ($randomSbus as $sbu) {
            $sbuSites = $sites->where('sbu_id', $sbu->id);
            $randomSbuSites = $sbuSites->random($faker->numberBetween(2, 3));
            $surveySiteIds = array_merge($surveySiteIds, $randomSbuSites->pluck('id')->toArray());
        }
        $survey->sites()->attach(array_unique($surveySiteIds));
        
        $questions = [
            ['text' => 'What is your work area/department?', 'type' => 'text', 'required' => true],
            ['text' => 'How would you rate the overall safety of your workplace?', 'type' => 'radio', 'required' => true],
            ['text' => 'Do you feel adequately trained on safety procedures?', 'type' => 'radio', 'required' => true],
            ['text' => 'Are safety equipment and protective gear readily available?', 'type' => 'radio', 'required' => true],
            ['text' => 'How often do you receive safety training updates?', 'type' => 'select', 'required' => true],
            ['text' => 'Have you witnessed any unsafe practices in the past month?', 'type' => 'radio', 'required' => true],
            ['text' => 'Do you feel comfortable reporting safety concerns?', 'type' => 'radio', 'required' => true],
            ['text' => 'How quickly are safety issues addressed when reported?', 'type' => 'radio', 'required' => true],
            ['text' => 'Are emergency procedures clearly communicated?', 'type' => 'radio', 'required' => true],
            ['text' => 'Which safety areas need the most improvement?', 'type' => 'checkbox', 'required' => false],
            ['text' => 'Have you experienced any workplace injuries in the past year?', 'type' => 'radio', 'required' => false],
            ['text' => 'What safety suggestions do you have?', 'type' => 'textarea', 'required' => false],
        ];
        
        foreach ($questions as $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'text' => $questionData['text'],
                'type' => $questionData['type'],
                'required' => $questionData['required'],
            ]);
        }
    }
    
    private function createPerformanceReviewSurvey($faker, $sbus, $sites, $admins)
    {
        $survey = Survey::create([
            'title' => 'Annual Performance Review - Self Assessment',
            'admin_id' => $admins->random()->id,
            'is_active' => true,
            'total_questions' => 0,
            'logo' => 'performance_logo.png',
        ]);
        
        // Assign to random SBUs and sites
        $randomSbus = $sbus->random($faker->numberBetween(1, 2));
        $survey->sbus()->attach($randomSbus->pluck('id'));
        
        $surveySiteIds = [];
        foreach ($randomSbus as $sbu) {
            $sbuSites = $sites->where('sbu_id', $sbu->id);
            $randomSbuSites = $sbuSites->random($faker->numberBetween(2, 4));
            $surveySiteIds = array_merge($surveySiteIds, $randomSbuSites->pluck('id')->toArray());
        }
        $survey->sites()->attach(array_unique($surveySiteIds));
        
        $questions = [
            ['text' => 'Employee Name', 'type' => 'text', 'required' => true],
            ['text' => 'Employee ID', 'type' => 'text', 'required' => true],
            ['text' => 'Review Period', 'type' => 'text', 'required' => true],
            ['text' => 'How would you rate your overall job performance?', 'type' => 'radio', 'required' => true],
            ['text' => 'How well did you meet your goals and objectives?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate your communication skills?', 'type' => 'radio', 'required' => true],
            ['text' => 'How well do you work as part of a team?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate your problem-solving abilities?', 'type' => 'radio', 'required' => true],
            ['text' => 'How well do you manage your time and priorities?', 'type' => 'radio', 'required' => true],
            ['text' => 'How would you rate your technical skills?', 'type' => 'radio', 'required' => true],
            ['text' => 'What were your major accomplishments this year?', 'type' => 'textarea', 'required' => true],
            ['text' => 'What challenges did you face and how did you overcome them?', 'type' => 'textarea', 'required' => true],
            ['text' => 'What are your goals for the next review period?', 'type' => 'textarea', 'required' => true],
            ['text' => 'What skills would you like to develop further?', 'type' => 'textarea', 'required' => false],
            ['text' => 'What support do you need from your supervisor?', 'type' => 'textarea', 'required' => false],
        ];
        
        foreach ($questions as $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'text' => $questionData['text'],
                'type' => $questionData['type'],
                'required' => $questionData['required'],
            ]);
        }
    }
}
