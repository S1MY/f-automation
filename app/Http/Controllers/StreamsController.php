<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stream;
use App\Models\StreamSettings;

class StreamsController extends Controller
{
    public function streamCreate(Request $request)
    {
        $vkStreamsRules = unserialize(Auth::user()->Subscribe->sub_config)['vk']['streams'];
        if($vkStreamsRules['type1'] !== 1){
            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Создание стримов недоступно для данного F-Аккаунта');
        }
        $rules = [
            'name' => 'required|string|max:20',
            'accType' => 'required|string|in:user,group',
            'accountId' => 'required|integer',
            'file.*' => 'required|file|mimetypes:video/avi,video/mp4',
            'duration' => 'required|string',
            'description' => 'required|string|max:200',
            'public' => 'required|in:0,1',
            'type' => 'required|string|in:interval,date,daily',
            'interval' => 'integer|in:0,60,120,240,360,480',
            'daily' => 'integer|in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23',
            'date' => 'string'
        ];
        $messages = [
            'name.required' => 'Обязательное поле',
            'name.string' => 'Не является строкой',
            'name.max' => 'Максимум :max символов',
            'accType.required' => 'Неопределённый аккаунт',
            'accType.string' => 'Неопределённый аккаунт',
            'accType.in' => 'Неопределённый тип',
            'accountId.required' => 'Обязательное поле',
            'accountId.integer' => 'Не является числом',
            'file.required' => 'Файл является обязательным',
            'file.file' => 'Файл не является файлом',
            'file.mimetypes' => 'Доступны только avi, mp4',
            'duration.required' => 'Файл является обязательным',
            'duration.string' => 'Ошибка длительности файлов',
            'description.required' => 'Обязательное поле',
            'description.string' => 'Не является строкой',
            'description.max' => 'Максимум :max символов',
            'public.required' => 'Должно быть числом',
            'public.in' => 'Должно быть 0 или 1',
            'type.required' => 'Должно быть числом',
            'type.string' => 'Должно быть строкой',
            'type.in' => 'Неизвестный тип операции',
            'interval.integer' => 'Интервал должен быть числом',
            'interval.in' => 'Неизвестный тип интервала',
            'daily.integer' => 'Ежедневный выбор не число',
            'daily.in' => 'Должно быть числом от 0 до 23',
            'date.string' => 'Дата имеет некорректный формат',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('input', $firstErrorKey, $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();
        if ($validatedData['accType'] == 'group') {
            $groupId = $validatedData['accountId'];
            $searchGroup = VkGroup::where('id', '=', $groupId)->first();
            if (!$searchGroup) {
                return $this->generateJsonResponse('input', 'account', 'Группа не найдена');
            }
            $accountId = $searchGroup->account_id;
            $groupId = $searchGroup->group_id;
            if($vkStreamsRules['type2'] !== 1){
                return $this->generateJsonResponse('error', 'Ошибка пакета', 'Создание стримов в группах недоступно для данного F-Аккаунта');
            }
        } else {
            $accountId = $validatedData['accountId'];
            $groupId = 0;
        }
        $searchAccount = VkAccount::where('id', '=', $accountId)->first();
        $ownerId = preg_replace('/[^0-9]/', '', $searchAccount->link);
        if (!$searchAccount) {
            return $this->generateJsonResponse('input', 'account', 'Аккаунт не найден');
        }
        if($validatedData['type'] === 'date'){
            if (Carbon::createFromFormat('d-m-Y H:i', $validatedData['date']) === false) {
                return $this->generateJsonResponse('input', 'tabs', 'Неверная дата');
            }
            
            if (Carbon::createFromFormat('d-m-Y H:i', $validatedData['date']) <= Carbon::now()->setTimezone('Europe/Moscow')) {
                return $this->generateJsonResponse('input', 'tabs', 'Дата и время меньше текущих');
            }
        }
        if($validatedData['type'] === 'interval'){
            if($validatedData['interval'] == 0){
                return $this->generateJsonResponse('input', 'tabs', 'Неверный интервал');
            }
        }
        $maxSeconds = 600;
        $postFiles = $validatedData['file'];
        if(count($postFiles) > 1){
            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Стримы поддерживают только 1 файл');
        }
        $durationItems = explode("_;_", $validatedData['duration']);
        foreach ($postFiles as $postFile) {
            $fileSize = $postFile->getSize();
            if ($fileSize > 419430400) {
                return $this->generateJsonResponse('input', 'file', 'Максимальный размер файла 50МБ');
            }
        }
        foreach ($durationItems as $durationItem) {
            if($durationItem > $maxSeconds){
                return $this->generateJsonResponse('input', 'file', 'Максимальная длительность видео'.$maxSeconds.' секунд');
            }
        }
        $linksDB = [];

        foreach ($postFiles as $postFile) {
            $fileName = 'stream-' . date("Y-m-d-H-i-s") . ".mp4";
            $userName = Auth::user()->login;
            Storage::disk('public')->putFileAs('files/users/' . $userName . '/streams', $postFile, $fileName);
            $fileLoadUrl = 'files/users/' . $userName . '/streams/';
            $link = $fileLoadUrl . $fileName;
            $linksDB[] = $link;
        }
        $streamCreate = Stream::create([
            'account_id' => $accountId,
            'group_id' => $groupId,
            'title' => $validatedData['name'],
            'description' => $validatedData['description'],
            'link' => serialize($linksDB),
            'active' => 1
        ]);
        $settingsData = [
            'stream_id' => $streamCreate->id,
        ];
        $timestampDate = date('Y-m-d H:i:s', strtotime('+1 minute'));
        if ($validatedData['type'] === 'date') {
            $settingsData['type'] = 'date';
            $timestamp = strtotime($validatedData['date']);
            $timestampDate = date('Y-m-d H:i:s', $timestamp);
            $settingsData['date'] = $timestampDate;
        } elseif ($validatedData['type'] === 'interval') {
            $settingsData['type'] = 'interval';
            $settingsData['interval'] = $validatedData['interval'];
            $streamCreate->updated_at = date('Y-m-d H:i:s', strtotime($timestampDate . ' -' . $validatedData['interval'] . ' minutes'));
            $streamCreate->save();
        }else{
            $settingsData['type'] = 'daily';
            $settingsData['daily'] = $validatedData['daily'];
            $currentTime = date('H:i:s');
            if ($currentTime < $validatedData['daily'].':00:00') {
                $streamCreate->updated_at = date('Y-m-d', strtotime('-1 day')) . substr($streamCreate->updated_at, 10);
            } else {
                $streamCreate->updated_at = date('Y-m-d') . substr($streamCreate->updated_at, 10);
            }
            $streamCreate->save();
        }
        StreamSettings::create($settingsData);
    }
    private function generateJsonResponse($type, $title, $message, $errorJson = null)
    {
        return response()->json([
            'status' => $type,
            'title' => $title,
            'message' => $message,
            'errorJson' => $errorJson
        ]);
    }
}
