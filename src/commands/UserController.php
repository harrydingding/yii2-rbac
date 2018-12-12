<?php

namespace harrydingding\rbac\console;

use yii\console\Controller;
use harrydingding\models\Admin;


/**
 * This is the command line version of HarryDing - a code generator.
 *
 * ```
 * $ ./yii rbac/user
 * ```
 * @author Harry Ding <harry.402@hotmail.com>
 */
class UserController extends Controller
{
    /**
     * Create user account
     */
    public function actionCreate()
    {
        echo "Create new user ...\n";             
        $username = $this->prompt('User Name:');  
        $email = $this->prompt('Email:'); 
        $password = $this->prompt('Password:'); 
        $model = new Admin();  
        $model->username = $username; 
        $model->email = $email; 
        $model->password = $password;
        if (!$model->save()) 
        {  
            echo 1111;die;
            foreach ($model->getErrors() as $error) 
            {  
                foreach ($error as $e)  
                {  
                    echo "$e\n";  
                }  
            }  
            return 1; 
        }  
        return 0;
    }
}
