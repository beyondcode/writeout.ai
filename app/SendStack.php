<?php

namespace App;


use Illuminate\Support\Facades\Http;

class SendStack
{
    protected $token;

    protected $baseUrl;

    public function __construct()
    {
        $this->token = config('services.sendstack.token');
        $this->baseUrl = config('services.sendstack.url');
    }

    public function isActiveSubscriber($email)
    {
        if (is_null($email)) {
            return false;
        }

        $subscriber = $this->getSubscriberDetails($email);

        if (is_null($subscriber) || $subscriber['status'] !== 'subscribed') {
            return false;
        }

        return true;
    }

    public function updateOrSubscribe($email, $tags = [])
    {
        $subscriber = $this->getSubscriberDetails($email);

        if (is_null($subscriber)) {
            $this->subscribe($email, $tags);

            return $subscriber;
        }

        if (count($tags) > 0) {
            $this->addTags($subscriber['uuid'], $tags);

            return $subscriber;
        }

        return $subscriber;
    }

    public function getSubscriberDetails($email)
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->get($this->baseUrl.'/subscribers/'.$email);

        if (!$response->ok()) {
            return null;
        }

        return $response->json();
    }

    protected function subscribe($email, $tags)
    {
        Http::withToken($this->token)
            ->acceptJson()
            ->post($this->baseUrl.'/subscribers/', [
                'email' => $email,
                'tags'  => $tags,
            ])->json();
    }

    protected function addTags($subscriberUuid, $tags)
    {
        Http::withToken($this->token)
            ->acceptJson()
            ->put($this->baseUrl.'/subscribers/'.$subscriberUuid, [
                'subscriber' => $subscriberUuid,
                'tags'       => $tags,
            ])->json();
    }
}
