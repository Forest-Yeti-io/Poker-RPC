<?php

namespace App\Infrastructure\Application\Service;

use App\Poker\Application\Exception\ApplicationException;
use App\Poker\Application\Service\RequestDecoratorInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestDecorator implements RequestDecoratorInterface
{
    private const string METHOD = 'Method';
    private const string PARAMS = 'Params';

    private string $method = '';
    private array $params = [];
    private bool $init = false;

    /**
     * @throws ApplicationException
     */
    public function init(Request $request): RequestDecoratorInterface
    {
        $content = $request->toArray();

        $method = $content[self::METHOD] ?? null;
        if (!is_string($method)) {
            throw new ApplicationException('Метод не определен в теле запроса');
        }

        $params = $content[self::PARAMS] ?? [];
        if (!is_array($params)) {
            throw new ApplicationException('Параметры не определены в теле запроса');
        }

        $this->method = $method;
        $this->params = $params;
        $this->init = true;

        return $this;
    }

    public function isInit(): bool
    {
        return $this->init;
    }

    /**
     * @throws ApplicationException
     */
    public function getMethod(): string
    {
        if (!$this->isInit()) {
            throw new ApplicationException('Запрос не инициализирован');
        }

        return $this->method;
    }

    /**
     * @throws ApplicationException
     */
    public function getParams(): array
    {
        if (!$this->isInit()) {
            throw new ApplicationException('Запрос не инициализирован');
        }

        return $this->params;
    }
}
