<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

<?= "<?php \n" ?>
$form = ActiveForm::begin([
	'id' => 'formulario',
	'enableClientScript' => true,
	'enableClientValidation' => true,
]);     
?>
	<?php foreach ($generator->getColumnNames() as $attribute) {
        if (in_array($attribute, $safeAttributes)) {
            echo "	<?=" . $generator->generateActiveField($attribute) . " ?>\n";
        }
    } ?>
		<?=" \n" ?>
		<div class="form-group">
			<?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Save') ?>, ['class' => 'btn btn-success']) ?>
		</div>

<?= "<?php " ?>ActiveForm::end(); ?>

</div>

<?="<?php \n" ?>
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
<?="?>" ?>

