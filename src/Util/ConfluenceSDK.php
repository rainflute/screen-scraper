<?php

/**
 * Created by PhpStorm.
 * User: ytan
 * Date: 10/27/16
 * Time: 11:14 PM
 */
class ConfluenceSDK
{
    /**
     * @var string $confluence_url
     */
    private $confluenceUrl;

    private $password;

    private $username;

    /**
     * ConfluenceRepository constructor.
     * @param string $url
     * @param string $username
     * @param string $password
     */
    public function __construct($url,$username,$password)
    {
        $this->confluenceUrl = $url;
        $this->password = $password;
        $this->username = $username;
    }

    /**
     * Create new page
     *
     * @param ConfluencePageModel $page
     * @return mixed
     * @throws \Exception
     */
    public function createPage($page){
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL=>$this->confluenceUrl."/content",
            CURLOPT_HEADER=>['Content-Type:application/json'],
            CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
            CURLOPT_USERPWD=> $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_PUT => 1,
            CURLOPT_POSTFIELDS => [
                'type' => $page->getType(),
                'title' => $page->getTitle(),
                'ancestors' => $page->getAncestors(),
                'space' => ['key'=>$page->getSpace()],
                'history' => [
                    'createdDate' => $page->getCreatedDate()
                        ? $page->getCreatedDate()
                        : date('Y-m-d\TH:i:s.uP', strtotime('now'))
                ],
                'body' => [
                    'storage'=>[
                        "value"=>$page->getContent(),
                        "representation"=>"storage",
                    ],
                ],
            ]
        ]);
        $serverOutput = curl_exec ($curl);
        curl_close ($curl);
        if (!$serverOutput) {
            throw new \Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        else {
            return json_encode($serverOutput);
        }
    }


    /**
     * Update an existing page
     *
     * @param ConfluencePageModel $page
     * @return mixed
     * @throws \Exception
     */
    public function updatePage($page){
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL=>$this->confluenceUrl."/content/{$page->getId()}",
            CURLOPT_HEADER=>['Content-Type:application/json'],
            CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
            CURLOPT_USERPWD=> $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'id' => $page->getId(),
                'type' => $page->getType(),
                'title' => $page->getTitle(),
                'space' => array('key'=>$page->getSpace()),
                'body' => array(
                    'storage'=>array(
                        "value"=>$page->getContent(),
                        "representation"=>"storage",
                    ),
                ),
                "version"=>array("number"=>$page->getVersion()),
            ));
        $serverOutput = curl_exec ($curl);
        curl_close ($curl);
        if (!$serverOutput) {
            throw new \Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        else {
            return json_encode($serverOutput);
        }
    }

    /**
     * Delete a page
     *
     * @param $id
     * @return null
     * @throws \Exception
     */
    public function deletePage($id){
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL=>$this->confluenceUrl."/content/$id",
            CURLOPT_HEADER=>['Content-Type:application/json'],
            CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
            CURLOPT_USERPWD=> $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => 'DELETE']
        );
        $serverOutput = curl_exec ($curl);
        curl_close ($curl);
        if (!$serverOutput) {
            throw new \Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        else {
            return json_encode($serverOutput);
        }
    }


    public function selectPageBy($parameters){
        $url = $this->confluenceUrl."/content?";
        if(isset($parameters['title'])){
            $url = $url."title={$parameters['title']}&";
        }
        if(isset($parameters['spaceKey'])){
            $url = $url."spaceKey={$parameters['spaceKey']}&";
        }
        if(isset($parameters['type'])){
            $url = $url."type={$parameters['type']}&";
        }
        if(isset($parameters['id'])){
            $url = $this->confluenceUrl."/content/".$parameters['id']."?";
        }
        if(isset($parameters['expand'])){
            $url = $url."expand=".$parameters['expand'];
        }
        $curl = curl_init();
        curl_setopt_array($curl,[
                CURLOPT_URL=>$url,
                CURLOPT_HEADER=>['Content-Type:application/json'],
                CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
                CURLOPT_USERPWD=> $this->username . ':' . $this->password,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'GET']
        );
        $serverOutput = curl_exec ($curl);
        curl_close ($curl);
        if (!$serverOutput) {
            throw new \Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        else {
            return json_encode($serverOutput);
        }
    }
    /**
     * Get page content by it's title and space
     * @param string $name
     * @param string $space
     * @param string $type
     * @return null
     * @throws \Exception
     */
    public function selectPageByName($name,$space,$type='page'){
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username,$this->password);
        $curl->setHeader('Content-Type','application/json');
        $name = str_replace(' ', '+', $name);
        $response = $curl->get($this->confluenceUrl."/content?title=$name&spaceKey=$space&type=$type");
        $curl->close();
        if ($curl->error) {
            throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        }
        else {
            return json_encode($response);
        }

    }

    /**
     * @param $pageId
     * @param string $expand
     * @return string
     * @throws \Exception
     */
    public function selectPageById($pageId,$expand='body.storage'){
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username,$this->password);
        $curl->setHeader('Content-Type','application/json');
        $response = $curl->get($this->confluenceUrl."/content/$pageId?expand=$expand");
        $curl->close();
        if ($curl->error) {
            var_dump($pageId);
            throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        }
        else {
            return json_encode($response);
        }
    }

    /**
     * Upload an attachment
     * @param $path
     * @param $parentPageId
     * @return string
     * @throws \Exception
     */
    public function uploadAttachment($path,$parentPageId){
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username,$this->password);
        $curl->setHeader('Content-Type','multipart/form-data');
        $curl->setHeader('X-Atlassian-Token','no-check');
        $response = $curl->post($this->confluenceUrl."/content/$parentPageId/child/attachment",array(
            'file' => '@' . $path
        ));
        $curl->close();
        if ($curl->error) {
            var_dump($parentPageId,$path);
            throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        }
        else {
            return json_encode($response);
        }
    }

    /**
     * Get attachments from the page
     *
     * @param $pageId
     * @return string
     * @throws \Exception
     */
    public function selectAttachments($pageId){
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username,$this->password);
        $curl->setHeader('Content-Type','application/json');
        $response = $curl->get($this->confluenceUrl."/content/$pageId/child/attachment");
        $curl->close();
        if ($curl->error) {
            throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        }
        else {
            return json_encode($response);
        }
    }

    /**
     * @param string $pageId
     * @param array $labels [['name'=>'example_tag'],...]
     * @return string
     * @throws \Exception
     */
    public function addLabel($pageId,$labels){
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username,$this->password);
        $curl->setHeader('Content-Type','application/json');
        $response = $curl->post($this->confluenceUrl."/content/$pageId/label",$labels);
        $curl->close();
        if ($curl->error) {
            throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        }
        else {
            return json_encode($response);
        }
    }
}