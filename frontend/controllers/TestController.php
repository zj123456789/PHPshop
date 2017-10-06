<?php
namespace frontend\controllers;
use yii\web\Controller;

class TestController extends Controller{
    //测试模板静态化--ob缓存
    public function actionTest(){
        $data = $this->renderPartial('@frontend/views/goods-category/index.php');
        file_put_contents(\Yii::getAlias('@frontend/web/index.html'),$data);
    }
    public function actionSend(){
        \Yii::$app->mailer->compose()
             ->setFrom('2421946649@qq.com')
             ->setTo('2421946649@qq.com')
             ->setSubject('zhujun')
            ->setHtmlBody('		
尊敬的用户，您好！
您的京西商城帐号于2017-09-20 11:42:30在中国-安徽-黄山（114.103.104.*）通过主站登录，系统检测此次登录存在高危风险。为保护您的帐号安全，系统暂时锁定了部分功能')
             ->send();
    }
}
