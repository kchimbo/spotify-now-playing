<?php

namespace Kchimbo\NowPlaying;

class NowPlaying
{
    protected $endpoint;


    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }


    public function getTrackInfo()
    {
        $r = $this->makeRequest();

        $response = json_decode($r);

        $e = $response->result;

        if (is_null($e)) return;

        $arr = [
            'album' => $e->album->name,
            'artist' => ($e->album->artists)[0]->name,
            'trackName' => $e->name,
            'artwork' => $this->getImage($e->uri),
            ];

        return $arr;

    }


    public function makeRequest()
    {
        $curl = curl_init();

        $post = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'core.playback.get_current_track' 
        ]; 
        
        $json = json_encode($post);

        $headers = [
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($json)
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 1, 
            CURLOPT_TIMEOUT => 3,
            CURLOPT_URL => $this->endpoint,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $json ]);

        $r = curl_exec($curl);
        
        if (!$r) return null;

        curl_close($curl);

        return $r; 
    }

    /**
     * @param array $lastTrack
     * @param $versionName
     *
     * @return string
     */
    protected function getImage($spotify)
    {
        $url = "https://embed.spotify.com/oembed/?url={$spotify}&format=json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; curl)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $json = curl_exec($ch);
        curl_close($ch);

        $json  = json_decode($json);

        $cover = $json->thumbnail_url;
        return $cover;
    }
}

