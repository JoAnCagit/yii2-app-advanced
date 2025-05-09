<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\mpdf\Pdf;
use kartik\icons\Icon;
use yii\bootstrap4\Modal;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>


/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;

?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <?= "<?php\n" ?>
    $columnas = [
            ['class' => 'kartik\grid\SerialColumn',
                'headerOptions' => ['width' => '40', 'class' => 'text-center',],
                'contentOptions' => ['class' => 'text-center',],
            ],
                <?php
                    $count = 0;
                    if (($tableSchema = $generator->getTableSchema()) === false) {
                        foreach ($generator->getColumnNames() as $name) {
                            if (++$count <> 1) {
                                echo "            '" . $name . "',\n";
                            } else {
                                echo "            //'" . $name . "',\n";
                            }
                        }
                    } else {
                        foreach ($tableSchema->columns as $column) {
                            $format = $generator->generateColumnFormat($column);
                            if (++$count <> 1) {
                                echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                            } else {
                                echo "            //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                            }
                        }
                    }
                ?>
            [
                'class' => 'kartik\grid\ActionColumn',
                'headerOptions' => ['width' => '80'],
                'contentOptions' => ['class' => 'text-center',],
                'template' => '{update} {view} {delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="fa fa-edit"></span>', '#', [
                            'id' => 'ventana',
                            'title' => 'Modificar',
                            'data-toggle' => 'modal',
                            'data-target' => '#idmodal',
                            'data-url' => Url::to(['update', 'id' => $key]),
                            'data-pjax' => '0',
                        ]);
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="fa fa-eye"></span>', '#', [
                            'id' => 'ventana',
                            'title' => 'Ver detalles',
                            'data-toggle' => 'modal',
                            'data-target' => '#idmodal',
                            'data-url' => Url::to(['view', 'id' => $key]),
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
    ]
    <?= "?>\n" ?>
<?= $generator->enablePjax ? "    <?php Pjax::begin(); \n" : "<?php \n" ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>
    try {
        <?= "echo " ?>GridView::widget([
            'id' => 'elgrid',
            'dataProvider' => $dataProvider,
            <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n " : ''; ?>
           'columns' => $columnas,
            'pjax' => <?= $generator->enablePjax ? "true" : "false"  ?>,
            'responsive' => true,
            'hover' => true,
            'condensed' => true,
            'striped' => false,
            'export'=>[
                'label' => 'Exportar',
                'showConfirmAlert'=>false,
                'target'=>GridView::TARGET_SELF,
                'header' => '',
            ],
            'exportConfig' => [
                GridView::PDF => [
                    'label' => 'Exportar a PDF',
                    'filename' => $this->title,
                    'config' => [
                        'format' => Pdf::FORMAT_LETTER,
                        'orientation' => Pdf::ORIENT_LANDSCAPE,
                        'destination' => Pdf::DEST_DOWNLOAD,
                        'methods' => [
                            'SetHeader' => ['<h5> '.$this->title.' </h5>||<p>La Empresa</p>'],
                            'SetFooter' => ['<div style="color:#aaa; font-weight: normal">Nombre</div> | {PAGENO} | <div style="color:#aaa; font-weight: normal">'. date("d/m/Y").'</div>'],
                            'SetAuthor' => 'JoAnCaSoft',
                        ],
                        'contentBefore' => '<span style="font-size:12pt;">'.$this->title.'</span>',
                    ],
                ],
                GridView::EXCEL => [
                    'label' => 'Exportar a Excel',
                    'filename' => $this->title,
                    'options' => ['title' => 'Microsoft Excel'],
                    'config' => [
                        'worksheet' => 'Listado',
                        'cssFile' => '',
                        'contentBefore' => '<span style="font-size:12pt;">'.$this->title.'</span>',
                    ]
                ],
            ],
    
            'toolbar'=>[
                '{export}', '&nbsp;',
                [
                    'content' =>
                    '<span class="row table-bordered ml-1 mr-1"> <span style="margin-top: 7px">&nbsp;&nbsp;Show: &nbsp; </span>'.
                    Html::dropDownList(
                        'perPage',
                        $dataProvider->pagination->pageSize,
                        [25 => '25', 50 => '50', 100 => '100', 500 => '500', 1000 => '1000'],
                        ['class' => 'form-control', 'style' => 'width: 78px;',
                         'onchange' => '$.pjax.reload({url: location.href,
                                                    data: {perPage: $(this).val()},
                                                    container: "#elgrid"})',
                        ]
                    ).
                    '</span>',
                ],
                '{toggleData}',
            ],
            'panel' => [
                'heading' => '<i class="fa fa-list-alt"></i> '.$this->title,
                'type' => 'light',
                'headingOptions' => ['class' => 'gridview-panel'],
                'before' => Html::a('Add', ['create'],
                            [
                                'data-pjax' => 0,
                                'class' => 'btn btn-outline-success',
                                'id' => 'ventana',
                                'data-toggle' => 'modal',
                                'data-target' => '#idmodal',
                                'data-url' => Url::to(['create']),
                            ]
                ),
            ],
        ]);
    <?php else: ?>
        <?= "<?= " ?>ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item'],
            'itemView' => function ($model, $key, $index, $widget) {
                return Html::a(Html::encode($model-><?= $generator->getNameAttribute() ?>), ['view', <?= $generator->generateUrlParams() ?>]);
            },
        ]) ?>
    <?php endif; ?>
    }
    catch (Exception $e) {
        echo "<pre>";
        echo $e;
    }
<?= $generator->enablePjax ? "    Pjax::end(); \n" : ''?>

	//------------------- Para mostrar la Ventana Modal ------------------
    Modal::begin([
        'id' => 'idmodal',
        'size' => 'modal-md',
        'title' => $this->title,
        'options' => [
        'tabindex' => false // important for Select2 to work properly
        ],
    ]);

    Modal::end();

	$this->registerJs("
        $(document).on('click', '#ventana', function() {
			$.get($(this).data('url'), function(data) {
				$('.modal-body').html(data);
				$('#idmodal').modal();
			});
		});
	");
<?="?>" ?>
</div>
