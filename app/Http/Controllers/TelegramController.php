<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\Auth;

use App\Models\TgAccount;
use App\Models\TgChannel;

use App\Models\TgStream;
use App\Models\TgStreamSettings;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings\Logger as LoggerSettings;

class TelegramController extends Controller
{
    public function streamCreate(Request $request)
    {
        // $vkStreamsRules = unserialize(Auth::user()->Subscribe->sub_config)['tg']['streams'];
        // if($vkStreamsRules['type1'] !== 1){
        //     return $this->generateJsonResponse('error', 'Ошибка пакета', 'Телеграм стримы недоступны для данного F-Аккаунта');
        // }
        $rules = [
            'name' => 'required|string|max:20',
            'channelId' => 'required|integer',
            'file.*' => 'required|file|mimetypes:video/avi,video/mp4',
            'duration' => 'required|string',
            'type' => 'required|string|in:interval,date,daily',
            'interval' => 'integer|in:0,60,120,240,360,480',
            'daily' => 'integer|in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23',
            'date' => 'string'
        ];
        $messages = [
            'name.required' => 'Обязательное поле',
            'name.string' => 'Не является строкой',
            'name.max' => 'Максимум :max символов',
            'channelId.required' => 'Обязательное поле',
            'channelId.integer' => 'Не является числом',
            'file.required' => 'Файл является обязательным',
            'file.file' => 'Файл не является файлом',
            'file.mimetypes' => 'Доступны только avi, mp4',
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
        $searchChannel = TgChannel::where('id', '=', $validatedData['channelId'])->first();
        if (!$searchChannel) {
            return $this->generateJsonResponse('input', 'channel', 'Канал не найден');
        }

        $apiId = env('TELEGRAM_APP_ID');
        $apiHash = env('TELEGRAM_APP_HASH');
        $userName = Auth::user()->login;
        $fileLoadUrl = 'files/users/' . $userName . '/tg_session';
        $logger = (new LoggerSettings)
            ->setType(Logger::FILE_LOGGER)
            ->setExtra(storage_path('app/public/'.$fileLoadUrl.'/madeline.log'))
            ->setLevel(Logger::FATAL_ERROR )
            ->setMaxSize(2048);
        $settings = 
        (new Settings())
        ->setAppInfo((new AppInfo)->setApiId($apiId)->setApiHash($apiHash))
        ->setLogger($logger);
        $sessionUrl = storage_path('app/public/' . $fileLoadUrl . '/session.madeline');
        $MadelineProto = new API($sessionUrl, $settings);
        $messages_Chats = $MadelineProto->channels->getAdminedPublicChannels(by_location: 0, check_limit: 0, );
        $channels = [];
        if(count($messages_Chats['chats']) < 1){
            return $this->generateJsonResponse('error', 'Ошибка канала', 'В вашем владении нет каналов');
        }else{
            foreach ($messages_Chats['chats'] as $chat) {
                $channels[] = $chat['username'];
            }
        }
        if(!in_array($searchChannel->name, $channels)){
            return $this->generateJsonResponse('error', 'Ошибка канала', 'Вы не являетесь администратором');
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
            Storage::disk('public')->putFileAs('files/users/' . $userName . '/tg_streams', $postFile, $fileName);
            $fileLoadUrl = 'files/users/' . $userName . '/tg_streams/';
            $link = $fileLoadUrl . $fileName;
            $linksDB[] = $link;
        }
        $searchAccount = TgAccount::where('uid', '=', Auth::user()->id)->first();
        $streamCreate = TgStream::create([
            'account_id' => $searchAccount->id,
            'channel_id' => $validatedData['channelId'],
            'title' => $validatedData['name'],
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
        TgStreamSettings::create($settingsData);
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
            $searchStream = TgStream::where('id', '=', $autoStart)
                ->first();
            $streamSettings = TgStreamSettings::where('stream_id', '=', $autoStart)
            ->first();
            $searchAccount = TgAccount::where('id', '=', $searchStream->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    if ($searchStream) {
                        $newActive = ($searchStream->active == 1) ? 0 : 1;
                        $tgStreamRules = unserialize(Auth::user()->Subscribe->sub_config)['tg']['streams'];
                        if($newActive ==1 && $tgStreamRules['type1'] !== 1){
                            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Телеграм стримы недоступны для данного F-Аккаунта');
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
            $searchStream = TgStream::where('id', '=', $removeId)
                ->first();
            $searchAccount = TgAccount::where('id', '=', $searchStream->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    if ($searchStream) {
                        TgStreamSettings::where('stream_id', '=', $removeId)->delete();
                        foreach (unserialize($searchStream->link) as $key => $link) {
                            Storage::disk('public')->delete($link);
                        }
                        TgStream::where('id', '=', $removeId)->delete();
                        return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Трансяция была удалена');
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
    public function tgChannelList(Request $request)
    {
        $userName = Auth::user()->login;
        $channelsResponse = [];
        $userId = Auth::user()->id;
        $accountId = $request->accountId;
        $apiId = env('TELEGRAM_APP_ID');
        $apiHash = env('TELEGRAM_APP_HASH');
        $fileLoadUrl = 'files/users/' . $userName . '/tg_session';
        $logger = (new LoggerSettings)
            ->setType(Logger::FILE_LOGGER)
            ->setExtra(storage_path('app/public/'.$fileLoadUrl.'/madeline.log'))
            ->setLevel(Logger::FATAL_ERROR )
            ->setMaxSize(2048);
        $settings = 
        (new Settings())
        ->setAppInfo((new AppInfo)->setApiId($apiId)->setApiHash($apiHash))
        ->setLogger($logger);
        $sessionUrl = storage_path('app/public/' . $fileLoadUrl . '/session.madeline');
        $MadelineProto = new API($sessionUrl, $settings);
        $messages_Chats = $MadelineProto->channels->getAdminedPublicChannels(by_location: 0, check_limit: 0, );
        $channels = [];
        return $this->generateJsonResponse('error', 'Ошибка канала', $messages_Chats['chats']);
        if(count($messages_Chats['chats']) < 1){
            return $this->generateJsonResponse('error', 'Ошибка канала', 'В вашем владении нет каналов');
        }else{
            foreach ($messages_Chats['chats'] as $chat) {
                $searchChannel = TgChannel::where('name', '=', $chat['username'])->first();
                if(!$searchChannel){
                    $channels[] = [
                        'title' => $chat['title'],
                        'username' => $chat['username']
                    ];
                }
            }
            return $this->generateJsonResponse('success', 'Список подгружен', $channels);
        }
    }
    public function tgChannelAuth(Request $request){
        $userName = Auth::user()->login;
        $accountId = $request->accountId;
        $channelName = $request->username;
        $userId = Auth::user()->id;
        $userName = Auth::user()->login;
        $apiId = env('TELEGRAM_APP_ID');
        $apiHash = env('TELEGRAM_APP_HASH');
        if (is_numeric($accountId)) {
            $searchAccount = TgAccount::where('id', '=', $accountId)
                ->where('uid', '=', $userId)
                ->first();
            if ($searchAccount) {
                $fileLoadUrl = 'files/users/' . $userName . '/tg_session';
                $logger = (new LoggerSettings)
                    ->setType(Logger::FILE_LOGGER)
                    ->setExtra(storage_path('app/public/'.$fileLoadUrl.'/madeline.log'))
                    ->setLevel(Logger::FATAL_ERROR )
                    ->setMaxSize(2048);
                $settings = 
                (new Settings())
                ->setAppInfo((new AppInfo)->setApiId($apiId)->setApiHash($apiHash))
                ->setLogger($logger);
                $sessionUrl = storage_path('app/public/' . $fileLoadUrl . '/session.madeline');
                $MadelineProto = new API($sessionUrl, $settings);
                $messages_Chats = $MadelineProto->channels->getAdminedPublicChannels(by_location: 0, check_limit: 0, );
                $channels = [];
                $searchChannel = TgChannel::where('name', '=', $channelName)->first();
                if(count($messages_Chats['chats']) < 1){
                    return $this->generateJsonResponse('error', 'Ошибка канала', 'В вашем владении нет каналов');
                }else{
                    if($searchChannel){
                        return $this->generateJsonResponse('error', 'Ошибка запроса', 'Данный канал уже приязан');
                    }
                    foreach ($messages_Chats['chats'] as $chat) {
                        if($chat['username'] === $channelName){
                            $channels[] = [
                                'title' => $chat['title'],
                                'username' => $chat['username']
                            ];
                        }
                    }
                    if (count($channels) < 1) {
                        return $this->generateJsonResponse('error', 'Ошибка запроса', 'Вы не являетесь администратором данного канала');
                    }
                    TgChannel::create([
                        'uid' => $userId,
                        'account_id' => $searchAccount->id,
                        'title' => $channels[0]['title'],
                        'name' => $channels[0]['username'],
                    ]);
                    return $this->generateJsonResponse('success', 'Всё успешно', 'Данный канал успешно привязан');
                }
            } else {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Данный аккаунт не был найден, либо вы не являетесь его владельцем');
            }
        }else {
            return $this->generateJsonResponse('error', 'Ошибка запроса', 'Неопределённое поле accountId');
        }
    }
    public function tgChannelRemove(Request $request){
        $userId = Auth::user()->id;
        $channelId = $request->channelId;
        if (is_numeric($channelId)) {
            $searchChannel = TgChannel::where('id', '=', $channelId)->where('uid', '=', $userId)->first();
            if($searchChannel){
                $tgStreams = TgStream::where('channel_id', '=', $channelId)->get();
                foreach ($tgStreams as $key => $stream) {
                    foreach (unserialize($stream->link) as $key => $link) {
                        Storage::disk('public')->delete($link);
                    }
                    TgStream::where('id', '=', $stream->id)->delete();
                }
                $searchChannel->delete();
                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Канал был удалён');
            }else{
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Данный канал не был найден, либо вы не являетесь его владельцем');
            }
        }else{
            return $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле channelId');
        }
    }
}
