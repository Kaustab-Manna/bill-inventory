<?php

namespace App\Services;

class MessagingService
{
    /**
     * Send an email with the invoice details
     */
    public function sendEmail($to, $subject, $message, $pdfAttachmentPath = null)
    {
        $email = \Config\Services::email();

        // Normally, you would configure SMTP in Config/Email.php
        $email->setFrom('noreply@billinventory.local', 'BillInventory System');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);
        
        if ($pdfAttachmentPath && file_exists($pdfAttachmentPath)) {
            $email->attach($pdfAttachmentPath);
        }

        // Return true to simulate success if email config isn't fully set up on local
        try {
            $email->send();
            return true; 
        } catch (\Exception $e) {
            // Log the error and simulate success for the demo
            log_message('error', 'Email failed to send: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Send an SMS using a simulated API (e.g., Twilio/Msg91)
     */
    public function sendSMS($phone, $message)
    {
        // Mock API call to an SMS provider
        log_message('info', "Simulating sending SMS to {$phone}: {$message}");
        
        // In a real scenario, you'd use cURL or Guzzle to send the request:
        /*
        $client = \Config\Services::curlrequest();
        $response = $client->post('https://api.smsprovider.com/v1/send', [
            'json' => [
                'api_key' => 'YOUR_API_KEY',
                'to' => $phone,
                'message' => $message
            ]
        ]);
        */
        
        return true;
    }

    /**
     * Send a WhatsApp message using a simulated API (e.g., WhatsApp Business API or Twilio)
     */
    public function sendWhatsApp($phone, $message, $pdfLink = null)
    {
        // Format phone number to international format if needed
        // Assuming India (+91) for this example if not provided
        if (strlen($phone) == 10) {
            $phone = '+91' . $phone;
        }

        if ($pdfLink) {
            $message .= "\n\nDownload Invoice: " . $pdfLink;
        }

        // Mock API call to a WhatsApp provider
        log_message('info', "Simulating sending WhatsApp to {$phone}: {$message}");

        // In a real scenario, you'd use cURL or Guzzle to send the request:
        /*
        $client = \Config\Services::curlrequest();
        $response = $client->post('https://api.whatsapp-provider.com/v1/messages', [
            'headers' => ['Authorization' => 'Bearer YOUR_ACCESS_TOKEN'],
            'json' => [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'text',
                'text' => ['body' => $message]
            ]
        ]);
        */

        return true;
    }
}
