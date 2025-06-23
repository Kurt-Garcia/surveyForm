<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\Sbu;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class LargeDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Get all SBUs and Sites
        $sbus = Sbu::all();
        $sites = Site::all();
        
        if ($sbus->isEmpty() || $sites->isEmpty()) {
            $this->command->error('Please run SbuAndSiteSeeder first to create SBUs and Sites');
            return;
        }
        
        $this->command->info('Creating Admins...');
        $this->createAdmins($faker, $sbus, $sites);
        
        $this->command->info('Creating Users...');
        $this->createUsers($faker, $sbus, $sites);
        
        $this->command->info('Creating Surveys...');
        $this->createSurveys($faker, $sbus, $sites);
        
        $this->command->info('Large data seeding completed!');
    }
    
    private function createAdmins($faker, $sbus, $sites)
    {
        $adminNames = [
            'John Anderson', 'Sarah Wilson', 'Michael Brown', 'Jessica Davis',
            'David Miller', 'Emily Johnson', 'Robert Garcia', 'Lisa Martinez',
            'Christopher Lee', 'Amanda Taylor', 'Matthew Thomas', 'Michelle White',
            'Andrew Harris', 'Jennifer Clark', 'Daniel Lewis', 'Stephanie Walker',
            'Ryan Hall', 'Rachel Young', 'Brandon King', 'Nicole Wright'
        ];
        
        $departments = ['HR', 'Operations', 'Quality Assurance', 'Finance', 'IT', 'Marketing'];
        
        foreach ($adminNames as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@company.com';
            
            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'contact_number' => $faker->numerify('09#########'),
                'password' => Hash::make('admin123'),
                'status' => $faker->randomElement([0, 1]), // Some active, some inactive
                'disabled_reason' => $faker->randomElement([null, 'Temporary suspension', 'Account under review']),
            ]);
            
            // Assign random SBUs to admin (1-3 SBUs per admin)
            $randomSbus = $sbus->random($faker->numberBetween(1, min(3, $sbus->count())));
            $admin->sbus()->attach($randomSbus->pluck('id'));
            
            // Assign random sites from assigned SBUs
            $adminSiteIds = [];
            foreach ($randomSbus as $sbu) {
                $sbuSites = $sites->where('sbu_id', $sbu->id);
                $randomSbuSites = $sbuSites->random($faker->numberBetween(1, min(3, $sbuSites->count())));
                $adminSiteIds = array_merge($adminSiteIds, $randomSbuSites->pluck('id')->toArray());
            }
            $admin->sites()->attach(array_unique($adminSiteIds));
        }
    }
    
    private function createUsers($faker, $sbus, $sites)
    {
        $firstNames = [
            'James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda',
            'William', 'Elizabeth', 'David', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica',
            'Thomas', 'Sarah', 'Christopher', 'Karen', 'Charles', 'Nancy', 'Daniel', 'Lisa',
            'Matthew', 'Betty', 'Anthony', 'Helen', 'Mark', 'Sandra', 'Donald', 'Donna',
            'Steven', 'Carol', 'Paul', 'Ruth', 'Andrew', 'Sharon', 'Joshua', 'Michelle',
            'Kenneth', 'Laura', 'Kevin', 'Sarah', 'Brian', 'Kimberly', 'George', 'Deborah',
            'Edward', 'Dorothy', 'Ronald', 'Lisa', 'Timothy', 'Nancy', 'Jason', 'Karen'
        ];
        
        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
            'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas',
            'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White',
            'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young',
            'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores'
        ];
        
        $positions = [
            'Customer Service Representative', 'Sales Associate', 'Team Lead', 'Supervisor',
            'Manager', 'Assistant Manager', 'Operations Staff', 'Quality Analyst',
            'Training Specialist', 'HR Coordinator', 'Finance Officer', 'IT Support'
        ];
        
        // Create 100 users
        for ($i = 0; $i < 100; $i++) {
            $firstName = $faker->randomElement($firstNames);
            $lastName = $faker->randomElement($lastNames);
            $name = $firstName . ' ' . $lastName;
            $email = strtolower($firstName . '.' . $lastName . $i) . '@company.com';
            
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'contact_number' => $faker->numerify('09#########'),
                'password' => Hash::make('user123'),
                'status' => $faker->randomElement([0, 1]),
                'disabled_reason' => $faker->randomElement([null, 'Inactive account', 'Pending verification']),
                'created_by' => Admin::inRandomOrder()->first()->id ?? null,
            ]);
            
            // Assign random SBUs to user (1-2 SBUs per user)
            $randomSbus = $sbus->random($faker->numberBetween(1, min(2, $sbus->count())));
            $user->sbus()->attach($randomSbus->pluck('id'));
            
            // Assign random sites from assigned SBUs
            $userSiteIds = [];
            foreach ($randomSbus as $sbu) {
                $sbuSites = $sites->where('sbu_id', $sbu->id);
                $randomSbuSites = $sbuSites->random($faker->numberBetween(1, min(2, $sbuSites->count())));
                $userSiteIds = array_merge($userSiteIds, $randomSbuSites->pluck('id')->toArray());
            }
            $user->sites()->attach(array_unique($userSiteIds));
        }
    }
    
    private function createSurveys($faker, $sbus, $sites)
    {
        $surveyTitles = [
            'Employee Satisfaction Survey 2024',
            'Customer Experience Feedback',
            'Workplace Culture Assessment',
            'Training Effectiveness Evaluation',
            'Annual Performance Review',
            'Product Quality Survey',
            'Service Delivery Assessment',
            'Team Collaboration Study',
            'Leadership Effectiveness Survey',
            'Work-Life Balance Questionnaire',
            'Communication Effectiveness Study',
            'Safety and Compliance Survey',
            'Innovation and Creativity Assessment',
            'Customer Support Experience',
            'Brand Awareness Survey',
            'Market Research Questionnaire',
            'Employee Engagement Study',
            'Process Improvement Survey',
            'Technology Usage Assessment',
            'Diversity and Inclusion Survey',
            'Career Development Feedback',
            'Compensation and Benefits Study',
            'Remote Work Experience Survey',
            'Onboarding Process Evaluation',
            'Performance Management Assessment',
            'Skills Development Survey',
            'Organizational Change Study',
            'Customer Loyalty Research',
            'Product Development Feedback',
            'Service Quality Evaluation'
        ];
        
        $questionTypes = ['text', 'textarea', 'radio', 'checkbox', 'select', 'number', 'email', 'date'];
        
        $sampleQuestions = [
            'How satisfied are you with your current role?',
            'What is your overall experience with our service?',
            'How would you rate the quality of our products?',
            'What improvements would you suggest?',
            'How likely are you to recommend us to others?',
            'What is your preferred communication method?',
            'How often do you use our services?',
            'What challenges do you face in your daily work?',
            'How would you rate your supervisor\'s support?',
            'What training programs would be most beneficial?',
            'How satisfied are you with the work environment?',
            'What motivates you most in your work?',
            'How clear are the company policies?',
            'What tools would help improve your productivity?',
            'How would you rate the team collaboration?',
            'What is your preferred work schedule?',
            'How satisfied are you with career advancement opportunities?',
            'What benefits are most important to you?',
            'How would you rate the company culture?',
            'What feedback do you have for management?'
        ];
        
        $admins = Admin::where('status', 1)->get();
        
        if ($admins->isEmpty()) {
            $this->command->error('No active admins found. Please create admins first.');
            return;
        }
        
        // Create 50 surveys
        foreach ($surveyTitles as $index => $title) {
            if ($index >= 50) break; // Limit to 50 surveys
            
            $survey = Survey::create([
                'title' => $title,
                'admin_id' => $admins->random()->id,
                'is_active' => $faker->boolean(80), // 80% chance of being active
                'total_questions' => 0, // Will be updated when questions are added
                'logo' => $faker->randomElement([null, 'logo1.png', 'logo2.png', 'company_logo.png']),
            ]);
            
            // Assign random SBUs to survey (1-2 SBUs per survey)
            $randomSbus = $sbus->random($faker->numberBetween(1, min(2, $sbus->count())));
            $survey->sbus()->attach($randomSbus->pluck('id'));
            
            // Assign random sites from assigned SBUs
            $surveySiteIds = [];
            foreach ($randomSbus as $sbu) {
                $sbuSites = $sites->where('sbu_id', $sbu->id);
                $randomSbuSites = $sbuSites->random($faker->numberBetween(1, min(3, $sbuSites->count())));
                $surveySiteIds = array_merge($surveySiteIds, $randomSbuSites->pluck('id')->toArray());
            }
            $survey->sites()->attach(array_unique($surveySiteIds));
            
            // Create 5-15 questions for each survey
            $questionCount = $faker->numberBetween(5, 15);
            for ($j = 0; $j < $questionCount; $j++) {
                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'text' => $faker->randomElement($sampleQuestions),
                    'type' => $faker->randomElement($questionTypes),
                    'required' => $faker->boolean(70), // 70% chance of being required
                ]);
            }
        }
    }
}
