<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Http\Resources\EmailTemplateResource;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\EntityDataHelper;

class EmailTemplateService
{
    /**
     * Get a paginated list of Email Templates.
     *
     * @param int $perPage
     * @return array
     */
    public function getEmailTemplates(int $perPage): array
    {
        $data = EmailTemplate::where('IsDeleted', false)->orderBy('CreatedOn','desc')->paginate($perPage);

        return [
            'templates' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new email template record.
     *
     * @param array $data The validated data for creating the email template
     * @return EmailTemplate The newly created email template model
     */
    public function createEmailTemplate(array $data): EmailTemplate
    {
        $validatedData = EntityDataHelper::prepareForCreation($data);
        return EmailTemplate::create($validatedData);
    }

    /**
     * Update an existing email template record.
     *
     * @param EmailTemplate $emailTemplate The email template model to update
     * @param array $data The validated data for updating the email template
     * @return EmailTemplate The updated email template model
     */
    public function updateEmailTemplate(EmailTemplate $emailTemplate, array $data): EmailTemplate
    {
        $validatedData = EntityDataHelper::prepareForUpdate($data);
        $emailTemplate->update($validatedData);
        return $emailTemplate;
    }
    
    /**
     * Delete an email template record.
     *
     * @param EmailTemplate $emailTemplate The email template model to delete
     * @return EmailTemplate The deleted email template model
     */
    public function deleteEmailTemplate(EmailTemplate $emailTemplate): EmailTemplate
    {
       $emailTemplate->update(['IsDeleted' => true]);
        return $emailTemplate;
    }
}
