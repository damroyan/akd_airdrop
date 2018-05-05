<?php
namespace Site\ApiV1\Controllers;

use \Response;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

/**
 * Class UploadController
 *
 * @property \Phalcon\Http\Request $request
 * @property \Phalcon\Http\Response $response
 * @package Site\ApiV1\Controllers
 */
class UploadController extends Controller {
    public function initialize() {
    }

    /**
     * Загрузка контента из админки
     *
     * @Role({"allow": ['user']})
     * @return \Phalcon\Http\Response
     */
    public function indexAction() {
        $params = $this->getParams();
        if (is_http_response($params)) { return $params; }

        $array = [
            'uploaded' => 0,
            'error' => [
                'message' => 'Upload file not found',
            ],
        ];

        $files = $this->request->getUploadedFiles();
        foreach($files as $file) {

            if(!$file->isUploadedFile()) {
                $array = [
                    'uploaded' => 0,
                    'error' => [
                        'message' => 'File upload error',
                    ],
                ];
                break;
            }

            $transactionManager = new TransactionManager();
            $transaction = $transactionManager->get();

            try {
                $upload = new \Model\Upload();
                $upload->setTransaction($transaction);

                $upload->assign([
                    'upload_name'       => $file->getName(),
                    'upload_type'       => mb_strtolower($file->getType()),
                    'upload_size'       => $file->getSize(),
                    'upload_real_type'  => mb_strtolower($file->getRealType()),
                    'upload_extension'  => mb_strtolower($file->getExtension()),
                    'user_id'           => $this->user->user_id,
                ]);

                if($upload->save() === false) {
                    $transaction->rollback("Can't save to DB");
                }

                preg_match('@(.*)(\.[a-z0-9]+)$@ui', $file->getName(), $match);
                if($match[1]) {
                    $name = \Helper\Translit::url("{$upload->upload_id}-{$match[1]}");
                }
                else {
                    $name = "{$upload->upload_id}";
                }

                $url = "{$this->config->image->directory_upload}/{$name}.{$upload->upload_extension}";

                if($file->moveTo(DOCUMENT_ROOT . $url)) {
                    $array = [
                        'uploaded'  => 1,
                        'fileName'  => $upload->upload_name,
                        'url'       => $url,
                    ];

                    $upload->assign([
                        'upload_path' => $url,
                    ]);

                    if($upload->save() === false) {
                        unlink(DOCUMENT_ROOT . $url);
                        $transaction->rollback("Can't save 'upload_path' to DB");
                    }

                    $transaction->commit();
                }
                else {
                    $array = [
                        'uploaded' => 0,
                        'error' => [
                            'message' => 'Error move upload file to upload directory',
                        ],
                    ];
                }
            }
            catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
                $array = [
                    'uploaded' => 0,
                    'error' => [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ],
                ];
            }

            break;
        }

        if(!$_GET['filebrowser']) {
            return (new \Response())->result($array);
        }

        $funcNum = $_GET['CKEditorFuncNum'] ;

        $response = new \Phalcon\Http\Response();
        $response->setHeader("Content-Type", "text/html");
        $response->setContent("<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '{$array['url']}', '{$array['error']['message']}');</script>");

        return $response;
    }

    /**
     * Загрузка картинок
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function tempAction() {
        $params = $this->getParams();
        if (is_http_response($params)) { return $params; }

        $files = $this->request->getUploadedFiles();
        switch(count($files)) {
            case 0:
                return \Response::Error('500', __('You try to download empty file'));
                break;

            case 1:
                break;

            default:
                return \Response::Error('500', __('Multiply download are not supported'));
                break;
        }

        foreach($files as $file) {
            if (!$file->isUploadedFile()) {
                return \Response::Error('500', __('Undefined file type'));
            }

            list($status, $error_code, $ext) = \Image::Validate($file->getTempName());
            if($status === false) {
                return \Response::Error('400', $error_code);
            }

            $random = md5($file->getName() . microtime(true) . rand(1000, 9999));
            $filename = "{$this->config->image->directory_tmp}/{$random}.{$ext}";

            if(!$file->moveTo(DOCUMENT_ROOT . "{$filename}")) {
                return \Response::Error('500', __('File system error! Please contact with support'));
            }

            return \Response::Ok([
                'file'      => $filename,
                'preview'   => array(
                    '1x1'       => \Helper\Image::getInstance()->image($filename, 256, 256, '1x1')->path(),
                ),
            ]);
        }

        return \Response::Error('500', 'error_upload');
    }
}
