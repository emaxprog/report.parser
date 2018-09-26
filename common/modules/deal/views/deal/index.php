<?php

use kartik\file\FileInput;

?>
<div class="jumbotron">
    <h1>Report</h1>
</div>
<form id="reportForm" action="<?= \yii\helpers\Url::to(['/deal/deal']) ?>" method="post" enctype='multipart/form-data'>
    <div class="form-group">
        <label for="reportFile">Report file</label>
        <?= FileInput::widget([
            'model' => $form,
            'attribute' => 'reportFile',
            'options' => ['id' => 'reportFile'],
            'pluginOptions' => [
                'required' => true,
                'showUpload' => false,
                'overwriteInitial' => true,
                'showRemove' => false,
                'showClose' => false,
                'fileActionSettings' => [
                    'showRemove' => false
                ],
            ],
        ]); ?>
    </div>
    <button type="submit" class="btn btn-primary" data-loading-text="Loading..." autocomplete="off">Build chart</button>
</form>

<div class="chart__container">
    <div id="chart"></div>
</div>