<?php


class Http
{
    /**
     * @var string
     */
    private $url;
    private $isPost;
    /**
     * @var array
     */
    private $fields;

    public function __construct()
    {
        $this->url = 'http://woo_correo_api.localhost.com/get_price';
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }

    public function setIsPost($val)
    {
        $this->isPost = $val;
        return $this;
    }

    public function setPostFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function send()
    {
        $ch = curl_init($this->url);

        if($this->isPost)
            curl_setopt($ch, CURLOPT_POST, true);

        $option = get_option('adue_woo_ca_conf');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'A2Auth: '.$option['adue_api_key'],
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen(http_build_query($this->fields)),
            'Accept: application/json',
            'User-Agent: PostmanRuntime/7.26.8',
            'Accept-Encoding: *',
            'Connection: keep-alive',
        ]);

        //curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($this->fields) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


}