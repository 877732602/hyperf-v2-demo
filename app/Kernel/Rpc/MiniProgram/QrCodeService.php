<?php
declare(strict_types = 1);

namespace App\Kernel\Rpc\MiniProgram;

use App\Helper\StringHelper;
use App\Kernel\MiniProgram\MiniProgramFactory;
use EasyWeChat\Kernel\Http\StreamResponse;
use Hyperf\RpcServer\Annotation\RpcService;
use Hyperf\Utils\Codec\Json;
use App\Kernel\Rpc\MiniProgram\Contract\QrCodeInterface;
use Throwable;

/**
 * Class QrCodeService
 * @package App\Kernel\Rpc\MiniProgram
 * @RpcService(name="QrCodeService",protocol="jsonrpc-tcp-length-check",server="jsonrpc",publishTo="consul")
 */
class QrCodeService extends BaseService implements QrCodeInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $channel, string $path, array $optional = [], string $fileName = '')
    {
        $this->logger->debug(sprintf('>>>>> 
            MiniProgram => QrCode => decryptData
            Channel:小程序通道[%s] Path[%s] Optional[%s] FileName[%s]
            <<<<<',
            $channel, $path, Json::encode($optional), $fileName));
        try {
            $response = retry($this->maxAttempts, function () use ($channel, $path, $optional)
            {
                return $this->container->get(MiniProgramFactory::class)->get($channel)->app_code->get($path, $optional);
            }, $this->sleep);
            if ($response instanceof StreamResponse) {
                if ($fileName !== '' && $fileName !== NULL) {
                    $fileName = $response->save($this->qrCodePath, (string)$fileName);
                } else {
                    $fileName = $response->save($this->qrCodePath, StringHelper::randString(10, 0));
                }
            }
        } catch (Throwable $throwable) {
            $this->logger->error(sprintf("
            >>>>> 
            EasyWechat:小程序通道[%s] {path}[%s] {optional}[%s] 获取小程序码(数量较少)发生错误,
            错误消息:{{%s}} 
            错误行号:{{%s}} 
            错误文件:{{%s}} 
            <<<<<
            ", $channel, $path, Json::encode($optional), $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        }
        finally {
            return $this->send($fileName);
        }
    }

    /**
     * @inheritDoc
     */
    public function getUnlimit(string $channel, string $scene, array $optional = [], string $fileName = '')
    {
        $this->logger->debug(sprintf('>>>>> 
            MiniProgram => QrCode => getUnlimit
            Channel:小程序通道[%s] Scene[%s] Optional[%s] FileName[%s]
            <<<<<',
            $channel, $scene, Json::encode($optional), $fileName));
        $response = NULL;
        try {
            $response = retry($this->maxAttempts, function () use ($channel, $scene, $optional)
            {
                return $this->container->get(MiniProgramFactory::class)->get($channel)->app_code->getUnlimit($scene, $optional);
            }, $this->sleep);
            if ($response instanceof StreamResponse) {
                if ($fileName !== '' && $fileName !== NULL) {
                    $fileName = $response->save($this->qrCodePath, (string)$fileName);
                } else {
                    $fileName = $response->save($this->qrCodePath, StringHelper::randString(10, 0));
                }
            }
        } catch (Throwable $throwable) {
            $this->logger->error(sprintf("
            >>>>> 
            EasyWechat:小程序通道[%s] {scene}[%s] {optional}[%s] 获取小程序码(数量较多)发生错误,
            错误消息:{{%s}} 
            错误行号:{{%s}} 
            错误文件:{{%s}} 
            <<<<<
            ", $channel, $scene, Json::encode($optional), $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        }
        finally {
            return $this->send($fileName);
        }
    }

    /**
     * @inheritDoc
     */
    public function getQrCode(string $channel, string $path, int $width = NULL, string $fileName = '')
    {
        $this->logger->debug(sprintf('>>>>> 
            MiniProgram => QrCode => getQrCode
            Channel:小程序通道[%s] Path[%s] Width[%s] FileName[%s]
            <<<<<',
            $channel, $path, $width, $fileName));
        $response = NULL;
        try {
            $response = retry($this->maxAttempts, function () use ($channel, $path, $width)
            {
                return $this->container->get(MiniProgramFactory::class)->get($channel)->app_code->getQrCode($path, $width);
            }, $this->sleep);
            if ($response instanceof StreamResponse) {
                if ($fileName !== '' && $fileName !== NULL) {
                    $fileName = $response->save($this->qrCodePath, (string)$fileName);
                } else {
                    $fileName = $response->save($this->qrCodePath, StringHelper::randString(10, 0));
                }
            }
        } catch (Throwable $throwable) {
            $this->logger->error(sprintf("
            >>>>> 
            EasyWechat:小程序通道[%s] {path}[%s] {width}[%s] 获取小程序码发生错误,
            错误消息:{{%s}} 
            错误行号:{{%s}} 
            错误文件:{{%s}} 
            <<<<<
            ", $channel, $path, $width, $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        }
        finally {
            return $this->send($fileName);
        }
    }
}


