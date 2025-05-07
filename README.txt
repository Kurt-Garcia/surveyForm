-------Run these in terminal to activate project-------

npm install

npm install @mui/material @emotion/react @emotion/styled

composer install

npm run dev

composer run dev

php artisan migrate

php artisan php artisan migrate --path=database/migrations/2025_04_15_055750_add_is_active_to_surveys_table.php

php artisan db:seed

php artisan db:seed --class=TestDataSeeder (For Individual Seeding)



-------To Test in the Phone-------
Open cmd

Type "ipconfig"

Find thd ipv4

php artisan serve --host=192.168.1.130 --port=8000







<a href="{{ route('admin.surveys.responses.show', ['survey' => $survey->id, 'account_name' => $response->account_name]) }}" 
    class="btn btn-sm" 
    style="border-color: var(--primary-color); color: var(--primary-color)"
    onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='white'"
    onmouseout="this.style.borderColor='var(--primary-color)'; this.style.backgroundColor='white'; this.style.color='var(--primary-color)'">
    <i class="bi bi-eye-fill me-1"></i>View Details
</a>