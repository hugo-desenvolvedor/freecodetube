<?php
/** @var $model \common\models\Video */

use yii\widgets\Pjax;

?>

<div class="row">
    <div class="col-sm-8">
        <div class="embed-responsive embed-responsive-16by9">
            <video
                    class="embed-responsive-item"
                    poster="<?= $model->getThumbnailLink() ?>"
                    src="<?= $model->getVideoLink() ?>"
                    allowfullscreen
                    controls
            ></video>
        </div>
        <h6 class="mt-2"><?= $model->title ?></h6>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?= $model->getViews()->count() ?> views &bull; <?= Yii::$app->formatter->asDate($model->created_at) ?>
            </div>
            <div>
                <?php Pjax::begin() ?>
                <?= $this->render(
                    '_buttons',
                    [
                        'model' => $model
                    ]
                ) ?>
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
    <div class="col-sm-8">

    </div>
</div>
