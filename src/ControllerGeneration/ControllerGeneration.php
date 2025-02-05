<?php

namespace Mlym\CodeGeneration\ControllerGeneration;


use Mlym\CodeGeneration\ClassGeneration\ClassGeneration;
use Mlym\CodeGeneration\ClassGeneration\MethodAbstract;
use Mlym\CodeGeneration\ControllerGeneration\Method\Add;
use Mlym\CodeGeneration\ControllerGeneration\Method\Delete;
use Mlym\CodeGeneration\ControllerGeneration\Method\GetList;
use Mlym\CodeGeneration\ControllerGeneration\Method\GetOne;
use Mlym\CodeGeneration\ControllerGeneration\Method\Edit;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiResponseParam;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccessParam;
use EasySwoole\HttpAnnotation\AnnotationTag\InjectParamsContext;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\Validate\Validate;
use Nette\PhpGenerator\PhpNamespace;

class ControllerGeneration extends ClassGeneration
{
    /**
     * @var $config ControllerConfig
     */
    protected $config;


    function addClassData()
    {
        $this->addUse($this->phpNamespace);
        $this->addGenerationMethod(new Add($this));
        $this->addGenerationMethod(new Edit($this));
        $this->addGenerationMethod(new GetOne($this));
        $this->addGenerationMethod(new GetList($this));
        $this->addGenerationMethod(new Delete($this));
    }

    function getClassName()
    {
        return $this->config->getRealTableName() . $this->config->getFileSuffix();
    }

    protected function getApiGroup()
    {
        $className = $this->getClassName();
        $namespace = $this->getConfig()->getNamespace();
        $namespace = str_replace('App\HttpController\\', '', $namespace);
        $namespace = str_replace('\\', '.', $namespace);
        return "{$namespace}.$className";
    }

    protected function addUse(PhpNamespace $phpNamespace)
    {
        $phpNamespace->addUse($this->config->getModelClass());
        $phpNamespace->addUse(Status::class);
        $phpNamespace->addUse(Validate::class);
        $phpNamespace->addUse(Validate::class);
        $phpNamespace->addUse($this->config->getExtendClass());
        //引入新版注解,以及文档生成
        $phpNamespace->addUse(ApiGroup::class);
        $phpNamespace->addUse(ApiGroupAuth::class);
        $phpNamespace->addUse(ApiGroupDescription::class);
        $phpNamespace->addUse(ApiFail::class);
        $phpNamespace->addUse(ApiRequestExample::class);
        $phpNamespace->addUse(ApiSuccess::class);
        $phpNamespace->addUse(Method::class);
        $phpNamespace->addUse(Param::class);
        $phpNamespace->addUse(Api::class);
        $phpNamespace->addUse(ApiSuccessParam::class);
        $phpNamespace->addUse(ApiDescription::class);
        $phpNamespace->addUse(ContextManager::class);
        $phpNamespace->addUse(InjectParamsContext::class);
    }

    function addGenerationMethod(MethodAbstract $abstract)
    {
        $this->methodGenerationList[$abstract->getMethodName()] = $abstract;
    }

    function addComment()
    {
        parent::addComment();
        $this->phpClass->addComment("@ApiGroup(groupName=\"{$this->getApiUrl()}/{$this->config->getRealTableName()}\")");
        $this->phpClass->addComment("@ApiGroupAuth(name=\"{$this->config->getAuthSessionName()}\")");
        $this->phpClass->addComment("@ApiGroupDescription(\"{$this->config->getTable()->getComment()}\")");
    }

    protected function getApiUrl()
    {
        $baseNamespace = $this->getConfig()->getNamespace();
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $baseNamespace);
        return $apiUrl;
    }

}
