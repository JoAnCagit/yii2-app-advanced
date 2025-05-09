<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\mpdf\Pdf;
use kartik\icons\Icon;
use yii\bootstrap4\Modal;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\usuarios\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');

?>
<div class="user-index">
    <?php
    $columnas = [
            ['class' => 'kartik\grid\SerialColumn',
                'headerOptions' => ['width' => '40', 'class' => 'text-center',],
                'contentOptions' => ['class' => 'text-center',],
            ],
            'username',
            'email:email',
            'status',
            'created_at',
            'updated_at',
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
    ?>
    <?php Pjax::begin(); 
    try {
        echo GridView::widget([
            'id' => 'elgrid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $columnas,
            'pjax' => true,
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
        }
    catch (Exception $e) {
        echo "<pre>";
        echo $e;
    }
    Pjax::end(); 

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
?></div>
