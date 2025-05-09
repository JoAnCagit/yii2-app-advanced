<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\usuarios\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

<?php 
$form = ActiveForm::begin([
	'id' => 'formulario',
	'enableClientScript' => true,
	'enableClientValidation' => true,
]);     
?>
		<?=$form->field($model, 'username')->textInput(['maxlength' => true]) ?>
	<?=$form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>
	<?=$form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>
	<?=$form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>
	<?=$form->field($model, 'email')->textInput(['maxlength' => true]) ?>
	<?=$form->field($model, 'status')->textInput() ?>
	<?=$form->field($model, 'created_at')->textInput() ?>
	<?=$form->field($model, 'updated_at')->textInput() ?>
	<?=$form->field($model, 'verification_token')->textInput(['maxlength' => true]) ?>
		 
		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

<?php ActiveForm::end(); ?>

</div>

<?php 
    $this->registerJs('
		$("form#formulario").on("beforeSubmit", function(e) {
			var form = $(this);
			$.post(
				form.attr("action")+"?submit=true",
				form.serialize()
			)
			.done(function(result) {
				form.parent().html(result.message);
				$.pjax.reload({container:"#elgrid"});
			});
			return false;
		}).on("submit", function(e){
			e.preventDefault();
			e.stopImmediatePropagation();
			return false;
		});
	');
?>
