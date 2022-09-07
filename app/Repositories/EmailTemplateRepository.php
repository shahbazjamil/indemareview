<?php

namespace App\Repositories;


use App\EmailTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

/**
 * Class EmailTemplateRepository
 */
class EmailTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'template_name',
        'subject',
        'body',
        'variables',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return EmailTemplate::class;
    }

    /**
     * @param array $input
     *
     * @param int $id
     *
     * @return bool
     */
    public function update($input, $id): bool
    {
        $emailTemplate = EmailTemplate::where('id', $id)->first();
        $fileName = null;
        $extension = null;

        if (isset($input['email_type']) && $input['email_type'] == '1'){
            $directory = "user-uploads/email-templates/$emailTemplate->id";
            if (File::exists(public_path($directory))) {
                $result = File::deleteDirectory(public_path($directory));
            }
        }

        if (isset($input['file']) && !empty($input['file'])) {
            $file = $input['file']->getClientOriginalName();
            $orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time() . mt_rand() . "." . $extension;

            $directory = "user-uploads/email-templates/$emailTemplate->id";
            if (File::exists(public_path($directory))) {
                $result = File::deleteDirectory(public_path($directory));
            }
        }

        $emailTemplate->update([
            'template_name' => $input['template_name'],
            'subject' => $input['subject'],
            'body' => $input['body'],
            'email_type' => $input['email_type'],
        ]);

        if (isset($input['file']) && !empty($input['file'])) {
            $emailTemplate->update([
                'file_name' => $fileName,
                'file_extension'  => $extension,
            ]);

            $directory = "user-uploads/email-templates/$emailTemplate->id";
            if (!File::exists(public_path($directory))) {
                $result = File::makeDirectory(public_path($directory), 0775, true);
            }
            $imageFilePath = "$directory/$fileName";

            File::move($input['file'], public_path($imageFilePath));
            $emailTemplate->save();
        }

        return true;
    }
}
