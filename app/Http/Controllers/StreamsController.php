<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stream;
use App\Models\StreamSettings;
use App\Models\StreamStory;
use App\Models\StreamComments;

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
            if (DateTime::createFromFormat('d-m-Y H:i', $validatedData['date']) === false) {
                return $this->generateJsonResponse('input', 'tabs', 'Неверная дата');
            }
            if(DateTime::createFromFormat('d-m-Y H:i', $validatedData['date']) <= new DateTime()){
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
        if($validatedData['type'] === 'interval'){
            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Стрим будет запущен в следующую минуту');
        }else{
            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Стрим будет запущен в указанное время');
        }
    }
    public function streamActivator(Request $request)
    {
        $userId = Auth::user()->id;
        $autoStart = $request->autoStart;
        if (is_numeric($autoStart)) {
            $searchStream = Stream::where('id', '=', $autoStart)
                ->first();
            $streamSettings = StreamSettings::where('stream_id', '=', $autoStart)
            ->first();
            $searchAccount = VkAccount::where('id', '=', $searchStream->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    if ($searchStream) {
                        $newActive = ($searchStream->active == 1) ? 0 : 1;
                        $vkStreamRules = unserialize(Auth::user()->Subscribe->sub_config)['vk']['streams'];
                        if($newActive ==1 && $vkStreamRules['type1'] !== 1){
                            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Активация стримов недоступна для данного F-Аккаунта');
                        }
                        if($searchStream->group_id > 0 && $vkStreamRules['type2'] !== 1){
                            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Активация стримов в группе недоступна для данного F-Аккаунта');
                        }
                        $searchStream->active = $newActive;
                        $timestampDate = date('Y-m-d H:i:s', strtotime('+1 minute'));
                        if ($streamSettings->type === 'date') {
                            $streamSettings->date = $timestampDate;
                            $streamSettings->save();
                        }
                        if($streamSettings->type === 'daily') {
                            $currentTime = date('H:i:s');
                            if ($currentTime < $streamSettings->daily.':00:00') {
                                $searchStream->updated_at = date('Y-m-d', strtotime('-1 day')) . substr($searchStream->updated_at, 10);
                            } else {
                                $searchStream->updated_at = date('Y-m-d') . substr($searchStream->updated_at, 10);
                            }
                        }
                        if($streamSettings->type === 'interval'){
                            $searchStream->updated_at = date('Y-m-d H:i:s', strtotime($timestampDate . ' -' . $streamSettings->interval . ' minutes'));
                        }
                        $searchStream->save();
                        if($newActive == 1){
                            if($streamSettings->type === 'daily'){
                                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Автозапуск включен. Стрим будет запущен в указанный час');
                            }else{
                                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Автозапуск включен. Стрим будет запущен через минуту');
                            }
                        }else{
                            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Автозапуск выключен. Стрим не будет выкладываться');
                        }
                    }else{
                        return $this->generateJsonResponse('error', 'Ошибка изменения', 'Данный стрим не найден');
                    }
                }else{
                    return $this->generateJsonResponse('error', 'Ошибка изменения', 'Данный стрим вам не принадлежит');
                }
            }else{
                return $this->generateJsonResponse('error', 'Ошибка изменения', 'Не найден родительский аккаунт');
            }
        }else{
            return $this->generateJsonResponse('error', 'Ошибка изменения', 'Неопределённое поле autoStart');
        }
    }
    public function streamRemove(Request $request)
    {
        $userId = Auth::user()->id;
        $removeId = $request->removeId;
        if (is_numeric($removeId)) {
            $searchStream = Stream::where('id', '=', $removeId)
                ->first();
            $searchAccount = VkAccount::where('id', '=', $searchStream->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    if ($searchStream) {
                        StreamStory::where('stream_id', '=', $removeId)->delete();
                        StreamComments::where('stream_id', '=', $removeId)->delete();
                        StreamSettings::where('stream_id', '=', $removeId)->delete();
                        $videoGetClient = new Client([
                            'base_uri' => 'https://api.vk.com',
                        ]);
                        $videoGetParams = [
                            'v' => '5.131',
                            'access_token' => $searchAccount->token
                        ];
                        if($searchStream->group_id > 0){
                            $videoGetParams['owner_id'] = '-'.$searchStream->group_id;
                        }else{
                            $videoGetParams['owner_id'] = preg_replace('/[^0-9]/', '', $searchAccount->link);
                        }
                        $videoGetGet = $videoGetClient->request('GET', '/method/video.get', [
                            'query' => $videoGetParams
                        ]);
                        if ($videoGetGet->getStatusCode() !== 200 || !$videoGetGet->getBody()) {
                            return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось найти старые трансляции F1');
                        }
                        $videoGetContent = $videoGetGet->getBody()->getContents();
                        $videoGetJSON = json_decode($videoGetContent);
                        if (isset($videoGetJSON->error)) {
                            return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось найти старые трансляции F2');
                        }
                        if($videoGetJSON->response->count > 0){
                            foreach ($videoGetJSON->response->items as $key => $item) {
                                if($item->title == $searchStream->title){
                                    $videoRemoveClient = new Client([
                                        'base_uri' => 'https://api.vk.com',
                                    ]);
                                    $videoRemoveParams = [
                                        'v' => '5.131',
                                        'access_token' => $searchAccount->token,
                                        'owner_id' => $item->owner_id,
                                        'video_id' => $item->id,
                                        'target_id' => $item->owner_id
                                    ];
                            
                                    $videoRemoveGet = $videoRemoveClient->request('GET', '/method/video.delete', [
                                        'query' => $videoRemoveParams
                                    ]);
                            
                                    if ($videoRemoveGet->getStatusCode() !== 200 || !$videoRemoveGet->getBody()) {
                                        return;
                                    }
                                    $videoRemoveContent = $videoRemoveGet->getBody()->getContents();
                                    $videoRemoveJSON = json_decode($videoRemoveContent);
                                    if (isset($videoRemoveJSON->error)) {
                                        return;
                                    }
                                }
                            }
                        }
                        foreach (unserialize($searchStream->link) as $key => $link) {
                            Storage::delete($link);
                        }
                        Stream::where('id', '=', $removeId)->delete();
                        return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Трансяция была удалена.<br> Страница автоматически перегрузится, чтобы изменения применились.');
                    }else{
                        return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данная трансяция не найдена');
                    }  
                }else{
                    return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данная трансяция вам не принадлежит');
                } 
            }else{
                return $this->generateJsonResponse('error', 'Ошибка удаления', 'Не найден родительский аккаунт');
            }
        }else{
            return $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле removeId');
        }
    }
}
