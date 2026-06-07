<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\NotificationLog;
use App\Services\CommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAppointmentBookedNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $appointmentId)
    {
    }

    public function handle(CommunicationService $communicationService): void
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($this->appointmentId);

        if (! $appointment) {
            return;
        }

        $doctorName = $appointment->doctor?->name ?? 'Doctor';
        $patientName = $appointment->patient?->name ?? 'Patient';

        $message = "Appointment booked: {$patientName} with {$doctorName} on {$appointment->appointment_date} at {$appointment->appointment_time}.";

        $patientEmail = $appointment->patient?->email ?? $appointment->patient?->user?->email;
        $doctorEmail = $appointment->doctor?->email ?? $appointment->doctor?->user?->email;
        $patientPhone = $appointment->patient?->phone ?? $appointment->patient?->user?->phone;
        $doctorPhone = $appointment->doctor?->phone ?? $appointment->doctor?->user?->phone;

        $this->sendEmail($patientEmail, 'Appointment Confirmation', $message, $appointment->patient?->user_id);
        $this->sendEmail($doctorEmail, 'New Appointment Assigned', $message, $appointment->doctor?->user_id);

        $this->sendSmsWhatsapp($communicationService, 'sms', $patientPhone, $message, $appointment->patient?->user_id);
        $this->sendSmsWhatsapp($communicationService, 'sms', $doctorPhone, $message, $appointment->doctor?->user_id);
        $this->sendSmsWhatsapp($communicationService, 'whatsapp', $patientPhone, $message, $appointment->patient?->user_id);
        $this->sendSmsWhatsapp($communicationService, 'whatsapp', $doctorPhone, $message, $appointment->doctor?->user_id);
    }

    private function sendEmail(?string $to, string $subject, string $message, ?int $userId): void
    {
        if (! $to) {
            return;
        }

        try {
            Mail::raw($message, function ($mail) use ($to, $subject) {
                $mail->to($to)->subject($subject);
            });

            NotificationLog::create(['user_id' => $userId, 'channel' => 'email', 'subject' => $subject, 'message' => $message, 'status' => 'sent']);
        } catch (\Throwable $e) {
            NotificationLog::create(['user_id' => $userId, 'channel' => 'email', 'subject' => $subject, 'message' => $e->getMessage(), 'status' => 'failed']);
        }
    }

    private function sendSmsWhatsapp(CommunicationService $service, string $channel, ?string $to, string $message, ?int $userId): void
    {
        if (! $to) {
            return;
        }

        try {
            $ok = $channel === 'sms' ? $service->sendSms($to, $message) : $service->sendWhatsapp($to, $message);

            NotificationLog::create(['user_id' => $userId, 'channel' => $channel, 'subject' => 'Appointment Notification', 'message' => $message, 'status' => $ok ? 'sent' : 'failed']);
        } catch (\Throwable $e) {
            NotificationLog::create(['user_id' => $userId, 'channel' => $channel, 'subject' => 'Appointment Notification', 'message' => $e->getMessage(), 'status' => 'failed']);
        }
    }
}
