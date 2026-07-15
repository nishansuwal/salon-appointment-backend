<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Do I need to book an appointment in advance?',
                'answer' => 'Yes, we recommend booking your appointment in advance to ensure your preferred date, time, and staff member are available.',
            ],
            [
                'question' => 'Can I choose my preferred stylist or staff member?',
                'answer' => 'Yes, while booking your appointment you can select your preferred staff member based on their availability.',
            ],
            [
                'question' => 'How can I cancel or reschedule my appointment?',
                'answer' => 'You can cancel or reschedule your appointment from your account before the scheduled appointment time.',
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept cash, credit/debit cards, and supported online payment methods.',
            ],
            [
                'question' => 'Will I receive an appointment confirmation?',
                'answer' => 'Yes, a confirmation email will be sent immediately after your appointment is successfully booked.',
            ],
            [
                'question' => 'What happens if I arrive late?',
                'answer' => 'Please inform us as soon as possible. Depending on our schedule, we may shorten or reschedule your appointment.',
            ],
            [
                'question' => 'Can I book multiple services in one appointment?',
                'answer' => 'Yes, you can select multiple services during a single booking.',
            ],
            [
                'question' => 'Do you accept walk-in customers?',
                'answer' => 'Walk-ins are welcome, but service availability depends on our current schedule. Appointments are recommended.',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}