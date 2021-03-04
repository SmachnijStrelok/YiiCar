<?php
namespace backend\controllers;

use common\models\Attachment;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Работа с файлами
 *
 * @package api\controllers
 */
class FileController extends Controller
{
    public $enableCsrfValidation = false;
    public function noAuthActions(): array
    {
        return ['download', 'upload'];
    }

    public function verbs()
    {
        return [
            'upload' => ['GET', 'POST'],
            'download' => ['GET', 'HEAD'],
            'delete' => ['DELETE']
        ];
    }

    public function actionUpload()
    {
        /*$form = new FileUploadForm();
        if (!$form->load(Yii::$app->request->bodyParams, '')) {
            throw new BadRequestHttpException('400');
        }

        Validation::throwOnInvalid($form);*/

        $file = UploadedFile::getInstanceByName('file');
        $systemFileName = uniqid('');
        $filePath = \Yii::$app->params['storagePath'] . '/' . $systemFileName;
        $mime = Yii::$app->request->getBodyParam('mime');

        $attachment = new Attachment();
        $attachment->name = Yii::$app->request->getBodyParam('original_name');
        $attachment->size = Yii::$app->request->getBodyParam('size');
        $attachment->extension = Yii::$app->request->getBodyParam('extension');
        $attachment->mime = $mime;
        $attachment->type = Yii::$app->request->getBodyParam('type');
        $attachment->path = $filePath;
        $attachment->created_at = time();

        if (!$attachment->save()) {
            throw new BadRequestHttpException('Upload error');
        }

        $file->saveAs($filePath);
        //rename($filename, $filePath);
        exec("chmod 644 " . $filePath);


        return json_encode($attachment->getAttributes());
    }


    /**
     * Получение файла
     *
     * При выполнении HEAD-запроса не возвращает файл,
     * но по статусу можно понять, может ли текущий юзер получить этот файл
     *
     * @get string entity_type сущность, к которой относится файл user_avatar|chat_attachment|post_attachment
     * @get int entity_id id сущности
     * @get int attachment_id id вложения
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDownload(){
        $userId = Yii::$app->user->id;
        $entityID = Yii::$app->request->get('entity_id');
        $entityType = Yii::$app->request->get('entity_type');
        $attachmentID = Yii::$app->request->get('attachment_id');

        // пока никаких проверок нет, поэтому сразу извлекаем по attachment_id

        switch ($entityType){
            case FileEntityTypes::POST_ATTACHMENT:
                Post::findOneOrFail(['id' => $entityID], 'Такого поста не существует');
                break;
            case FileEntityTypes::CHAT_ATTACHMENT:
                ChatMessage::findOneOrFail(['id' => $entityID], 'Такого сообщения не существует');
                break;
            case FileEntityTypes::QUESTION_ATTACHMENT:
                Question::findOneOrFail(['id' => $entityID], 'Такого вопроса не существует');
                break;
            case FileEntityTypes::QUESTION_ANSWER_ATTACHMENT:
                QuestionAnswer::findOneOrFail(['id' => $entityID], 'Такого ответа не существует');
                break;
            case FileEntityTypes::REQUEST_TO_AUTHORITY_ATTACHMENT:
                RequestToAuthority::findOneOrFail(['id' => $entityID], 'Такого запроса в орган не существует');
                break;
            case FileEntityTypes::REQUEST_TO_AUTHORITY_PDF:
                $request = RequestToAuthority::findOneOrFail(['id' => $entityID], 'Такого запроса в орган не существует');
                if(
                    ($request->author_id != $userId && !Yii::$app->user->can(UserRoles::MODERATOR))
                ){
                    throw new ForbiddenHttpException('access denied!');
                }
                if(!$request->pdf_id){
                    $converter = new RequestToAuthorityTextConverter($request);
                    $converter->savePdf();
                }
                $attachmentID = $request->pdf_id;
                break;
            case FileEntityTypes::REQUEST_SHIPMENT_PDF:
                $request = RequestShipment::findOneOrFail(['id' => $entityID], 'Такого запроса в орган не существует');
                if(
                    ($request->author_id != $userId && !Yii::$app->user->can(UserRoles::MODERATOR))
                ){
                    throw new ForbiddenHttpException('access denied!');
                }
                if(!$request->pdf_id){
                    $converter = new RequestToAuthorityTextConverter($request);
                    $converter->savePdf();
                }
                $attachmentID = $request->pdf_id;
                break;
            case FileEntityTypes::ANSWER_ON_REQUEST_SHIPMENT_ATTACHMENT:
                $answer = RequestShipmentAnswer::findOneOrFail(['id' => $entityID], 'Такого ответа на запрос в орган не существует');
                RequestToAuthorityAccessor::canGetRequestShipmentAnswer($answer);
                break;
            case FileEntityTypes::DOCUMENT_ATTACHMENT:
                Document::findOneOrFail(['id' => $entityID], 'Такого документа не существует');
                break;
            case FileEntityTypes::DOCUMENT_ANSWER_ATTACHMENT:
                DocumentAnswer::findOneOrFail(['id' => $entityID], 'Такого ответа на документ не существует');
                break;
            case FileEntityTypes::SERVICE_ATTACHMENT:
                Service::findOneOrFail(['id' => $entityID], 'Такой услуги не существует');
                break;
            case FileEntityTypes::NDFL_SERVICE_ATTACHMENT:
                NdflService::findOneOrFail(['id' => $entityID], 'Такой услуги не существует');
                break;
            case FileEntityTypes::CHAT_ICON:
                ChatUser::findOneOrFail(['chat_id' => $entityID, 'user_id' => $userId], 'У вас нет доступа к файлу');
                break;
            default:
                throw new BadRequestHttpException('Передан неверный тип вложения');
        }

        $attachment = Base::findOne(['id' => $attachmentID]);
        $filePath = $attachment->path;

        if (Yii::$app->request->method == 'HEAD') {
            if (file_exists($filePath)) {
                return;
            } else {
                throw new NotFoundHttpException();
            }
        }

        return Yii::$app->response->xSendFile(
            $filePath,
            $attachment->name,
            ['xHeader' => 'X-Accel-Redirect']
        );

    }


    /**
     * @param $entity_id
     * @param $entity_type
     * @param $attachment_id
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionDelete($entity_id, $entity_type, $attachment_id){
        $userId = Yii::$app->user->id;
        $attachment_id = (int)$attachment_id;


        $transaction = Yii::$app->db->beginTransaction();
        try {
            switch ($entity_type){
                case FileEntityTypes::POST_ATTACHMENT:
                    $this->deletePostAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::CHAT_ATTACHMENT:
                    $this->deleteChatAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::QUESTION_ATTACHMENT:
                    $this->deleteQuestionAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::QUESTION_ANSWER_ATTACHMENT:
                    $this->deleteQuestionAnswerAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::REQUEST_TO_AUTHORITY_ATTACHMENT:
                    $this->deleteRequestToAuthorityAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::ANSWER_ON_REQUEST_SHIPMENT_ATTACHMENT:
                    $this->deleteAnswerOnRequestToAuthorityAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::USER_AVATAR:
                    $this->deleteUserAvatar($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::DOCUMENT_ATTACHMENT:
                    $this->deleteDocumentAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::DOCUMENT_ANSWER_ATTACHMENT:
                    $this->deleteDocumentAnswerAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::SERVICE_ATTACHMENT:
                    $this->deleteServiceAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::NDFL_SERVICE_ATTACHMENT:
                    $this->deleteNdflServiceAttachment($entity_id, $attachment_id, $userId);
                    break;
                case FileEntityTypes::CHAT_ICON:
                    $this->deleteChatIcon($entity_id, $attachment_id, $userId);
                    break;
                default:
                    throw new BadRequestHttpException('Передан неверный тип вложения');
            }

            $attachment = Base::findOne(['id' => $attachment_id]);
            $attachment->delete();

        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $transaction->commit();

    }

    private function deletePostAttachment($entity_id, $attachment_id, $userId){
        $post = Post::findOne(['id' => $entity_id]);
        if(!$post){
            throw new BadRequestHttpException('Такого поста не существует');
        }

        if($post->user_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором этого поста');
        }

        // TODO статус для постов

        $postAttachment = PostAttachment::findOne(['post_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$postAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $postAttachment->delete();
    }

    private function deleteChatAttachment($entity_id, $attachment_id, $userId){
        $message = ChatMessage::findOne(['id' => $entity_id]);
        if (!$message) {
            throw new BadRequestHttpException('Такого сообщения не существует');
        }

        if($message->sender_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором этого сообщения');
        }

        if($message->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у отправленного сообщения');
        }

        $chatAttachment = ChatAttachment::findOne(['message_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$chatAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $chatAttachment->delete();
    }

    private function deleteQuestionAttachment($entity_id, $attachment_id, $userId){
        $question = Question::findOne(['id' => $entity_id]);

        if(!$question){
            throw new NotFoundHttpException('Такого вопроса не существует');
        }

        if($question->author_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором этого вопроса');
        }

        if($question->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного вопроса');
        }

        $questionAttachment = QuestionAttachment::findOne(['question_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$questionAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $questionAttachment->delete();
    }

    private function deleteQuestionAnswerAttachment($entity_id, $attachment_id, $userId){
        $questionAnswer = QuestionAnswer::findOne(['id' => $entity_id]);

        if(!$questionAnswer){
            throw new NotFoundHttpException('Такого ответа не существует');
        }

        if($questionAnswer->author_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором этого ответа на вопрос');
        }

        if($questionAnswer->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного ответа на вопрос');
        }

        $questionAnswerAttachment = QuestionAnswerAttachment::findOne(['answer_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$questionAnswerAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $questionAnswerAttachment->delete();
    }

    private function deleteRequestToAuthorityAttachment($entity_id, $attachment_id, $userId){
        $request = RequestToAuthority::findOne(['id' => $entity_id]);

        if(!$request){
            throw new NotFoundHttpException('Такого обращения не существует');
        }

        if($request->author_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором этого вопроса');
        }

        if($request->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного вопроса');
        }

        $requestAttachment = RequestAttachment::findOne(['request_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$requestAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $requestAttachment->delete();
    }

    private function deleteAnswerOnRequestToAuthorityAttachment($entity_id, $attachment_id, $userId){
        $answer = RequestShipmentAnswer::findOne(['id' => $entity_id]);

        if(!$answer){
            throw new NotFoundHttpException('Такого ответа на обращение не существует');
        }

        if(!Yii::$app->user->can(UserRoles::ADMIN)){
            throw new BadRequestHttpException('У вас нет прав для удаления файла');
        }

        if($answer->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного ответа на обращение');
        }

        $requestAttachment = RequestShipmentAnswerAttachment::findOne(['answer_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$requestAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $requestAttachment->delete();
    }

    private function deleteUserAvatar($entity_id, $attachment_id, $userId){

    }

    private function deleteDocumentAttachment($entity_id, $attachment_id, $userId){
        $document = Document::findOne(['id' => $entity_id]);

        if(!$document){
            throw new NotFoundHttpException('Такого документа не существует');
        }

        if($document->author_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором этого документа');
        }

        if($document->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного документа');
        }

        $documentAttachment = DocumentAttachment::findOne(['document_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$documentAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $documentAttachment->delete();
    }

    private function deleteDocumentAnswerAttachment($entity_id, $attachment_id, $userId){
        $answer = DocumentAnswer::findOne(['id' => $entity_id]);

        if(!$answer){
            throw new NotFoundHttpException('Такого ответа не существует');
        }

        if($answer->lawyer_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором этого ответа');
        }

        if($answer->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного ответа');
        }

        $answerAttachment = DocumentAnswerAttachment::findOne(['answer_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$answer){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $answerAttachment->delete();
    }

    private function deleteServiceAttachment($entity_id, $attachment_id, $userId){
        $service = Service::findOne(['id' => $entity_id]);

        if(!$service){
            throw new NotFoundHttpException('Такой услуги не существует');
        }

        if($service->author_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором запроса на услугу');
        }

        if($service->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного запроса на услугу');
        }

        $serviceAttachment = ServiceAttachment::findOne(['service_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$serviceAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $serviceAttachment->delete();
    }

    private function deleteNdflServiceAttachment($entity_id, $attachment_id, $userId){
        $service = NdflService::findOne(['id' => $entity_id]);

        if(!$service){
            throw new NotFoundHttpException('Такой услуги не существует');
        }

        if($service->author_id != $userId){
            throw new BadRequestHttpException('Вы не являетесь автором запроса на услугу');
        }

        if($service->state != States::DRAFT){
            throw new BadRequestHttpException('Вы не можете удалить файл у опубликованного запроса на услугу');
        }

        $serviceAttachment = NdflServiceAttachment::findOne(['service_id' => $entity_id, 'attachment_id' => $attachment_id]);
        if(!$serviceAttachment){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $serviceAttachment->delete();
    }

    private function deleteChatIcon($entity_id, $attachment_id, $userId){
        $chat = Chat::findOne(['id' => $entity_id, 'creator_id' => $userId]);
        if (!$chat) {
            throw new BadRequestHttpException('Вы не являетесь автором этого чата');
        }

        $chat->icon_id = null;
        $chat->save();

        /*$icon = Base::findOne(['id' => $chat->icon_id]);
        if(!$icon){
            throw new NotFoundHttpException('Вложение не найдено');
        }

        $icon->delete();*/
    }

}
