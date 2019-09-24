@extends('layouts.management')

@section('content')
<div class="container" id="mainContainer">
	<div class="row">
		<div class="col-12">
			<h5>Основные темы скриптов</h5><br>
	@foreach($questionsList as $thisTopic)
			<div class="card mb-3">
				<div class="card-header">
					{{ $thisTopic['topic_name'] }}
				</div>
				<div class="card-body">
					<div id="visualNetwork{{ $thisTopic['id'] }}" class="visualNetwork"></div>
					<script type="text/javascript">
						var nodes = new vis.DataSet([
		@foreach($thisTopic['questions'] as $thisQuestion)
							{id: '{{ $thisQuestion['topic'] }}_{{ $thisQuestion['id'] }}', label: '{{ $thisQuestion['question_text'] }}'},
		@endforeach
						]);
						var edges = new vis.DataSet([
		@foreach($thisTopic['questions'] as $thisQuestion)
			@php 
				$variantsExplained = json_decode($thisQuestion['variants'], 1);
			@endphp
			@if($thisQuestion['parent_id'] != -1)
							{from: '{{ $thisQuestion['topic'] }}_{{ $thisQuestion['id'] }}', to: '{{ $thisQuestion['topic'] }}_{{ $thisQuestion['parent_id'] }}', arrows: 'from'},
				@if(count($variantsExplained)>0)
					@foreach($variantsExplained as $thisExplainedVar)
							{from: '{{ $thisQuestion['topic'] }}_{{ $thisExplainedVar['link'] }}', to: '{{ $thisQuestion['topic'] }}_{{ $thisQuestion['id'] }}', arrows: 'from'},
					@endforeach
				@endif
			@endif
		@endforeach
						]);
						var container = document.getElementById('visualNetwork{{ $thisTopic['id'] }}');
						var data = {
							nodes: nodes,
							edges: edges
						};
						var options = {
							layout: {
								hierarchical: {
									enabled: true,
									direction: 'DU',
									sortMethod: 'directed',
									nodeSpacing: 50,
									treeSpacing: 50,
									levelSeparation: 100,
									edgeMinimization: false,
								}
							},
							nodes: {
								shape: 'box'
							},
							physics: {
								enabled: true,
								hierarchicalRepulsion: {
									centralGravity: 0.0,
									springLength: 200,
									springConstant: 0.01,
									nodeDistance: 150,
									damping: 0.09
								}
							}
						};
						var network = new vis.Network(container, data, options);
						network.on('click', function(properties) {
							var clickedEdge = properties.nodes[0].split('_');
						    $('.action-buttons-' + clickedEdge[0]).fadeIn().attr('data-id', clickedEdge[1]);
						});
					</script>
				</div>
				<div class="card-footer" align="right" >
					<input type="button" class="btn btn-sm btn-secondary edit-question action-buttons action-buttons-{{ $thisTopic['id'] }}" data-id="" data-action="edit" value="Правка">
					<input type="button" class="btn btn-sm btn-secondary add-question action-buttons action-buttons-{{ $thisTopic['id'] }}" data-id="" data-topic="{{ $thisQuestion['topic'] }}" data-action="add" value="Добавить">
					<input type="button" class="btn btn-sm btn-secondary remove-question action-buttons action-buttons-{{ $thisTopic['id'] }}" data-id="" data-action="remove" value="Удалить">
				</div>
			</div>
	@endforeach
		</div>
	</div>
</div>
<div id="editQuestionModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<form id="editQuestionForm">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Правка вопроса</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12 mb-3">
							Текст вопроса:
							<textarea id="editQuestionText" name="question_text" class="form-control"></textarea>
						</div>
						<div class="col-sm-12">Варианты ответов:</div>
						<div class="col-sm-12 mb-1 row" id="editQuestionVariants"></div>
						<div class="col-sm-12">
							<input type="button" class="btn btn-sm btn-outline-success w-100 add-variants" value="Добавить">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary edit-question-save">Сохранить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="addQuestionModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Создание нового вопроса</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 mb-3">
						Текст вопроса:
						<textarea id="addQuestionText" name="question_text" class="form-control"></textarea>
					</div>
					<div class="col-sm-12">Варианты ответов:</div>
					<div class="col-sm-12 mb-1 row" id="addQuestionVariants"></div>
					<div class="col-sm-12">
						<input type="button" class="btn btn-sm btn-outline-success w-100 add-variants" value="Добавить">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary add-question-save">Создать</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
			</div>
		</div>
	</div>
</div>
<div id="removeQuestionModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Удаление вопроса</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Modal body text goes here.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary">Да, удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Нет, отменить</button>
			</div>
		</div>
	</div>
</div>
<script>
	$.ajaxSetup(
		{
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		}
	);
	var linkedQuestions = {};
	$('.action-buttons').on('click', function() {
		if($(this).attr('data-id') > 0){
			$('#mainContainer').attr('data-last-action', $(this).attr('data-action'));
			$.post(
				'/management/getQuestionDetails', 
				{ 
					question_id: $(this).attr('data-id')
				}, 
				function(response) {
					$('#editQuestionText').val(response.question_text);
					var variants = jQuery.parseJSON(response.variants);
					$('#editQuestionVariants').empty();
					$.each(variants, function( index, value ) {
						var appendedEl = '\
							<div class="col-sm-5 mb-1 variant-' + value.id + '">\
								<input type="hidden" class="editedVariantId" value="' + value.id + '">\
								<input type="text" class="editedVariantTitle form-control" value="' + value.title + '">\
							</div>\
							<div class="col-sm-6 mb-1 variant-' + value.id + '">\
								<select class="form-control editedVariantValue" id="variant-link-' + value.id + '">\
									<option value="0">Назначить позже</option>\
									<option value="-1">Тупиковая ветка</option>\
									<option value="-2">Успешное завершение</option>';
						linkedQuestions = response.linked;
						$.each(response.linked, function( linkedIndex, linkedValue ) {
							appendedEl = appendedEl + '\
									<option value="' + linkedIndex + '">' + linkedValue + '</option>';
						});
						appendedEl = appendedEl + '\
								</select>\
							</div>\
							<div class="col-sm-1 mb-1 variant-' + value.id + '">\
								<input type="button" class="btn btn-sm btn-outline-danger remove-variant" data-variant-id="' + value.id + '" value="X">\
							</div>';
						$('#editQuestionVariants').append(appendedEl);
						$('#variant-link-' + value.id).val(value.link);
					});
				}
			);
			if($(this).attr('data-action') == "edit"){
				$('.edit-question-save').attr('data-id', $(this).attr('data-id'));
				$('#editQuestionModal').modal();
			}else if($(this).attr('data-action') == "add"){
				/*
					Здесь нужно получить список связанных вопросов перед тем, как что-то выводить.
				*/
				$('.add-question-save').attr('data-parent', $(this).attr('data-id'));
				$('.add-question-save').attr('data-topic', $(this).attr('data-topic'));
				$('#addQuestionModal').modal();
			}else if($(this).attr('data-action') == "remove"){
				$('#removeQuestionModal').modal();
			}
		}else{
			alert('Сначала выберите вопрос');
		}
	});
	$(document.body).on('click', '.remove-variant', function(e) {
		$('.variant-' + $(this).attr('data-variant-id')).remove();
	});
	$(document.body).on('click', '.add-variants', function(e) {
		/*
			Здесь должно быть получение уникального ID
		*/
		var fieldsPref = '';
		if($('#mainContainer').attr('data-last-action') == "add"){
			fieldsPref = 'created';
		}else if($('#mainContainer').attr('data-last-action') == "edit"){
			fieldsPref = 'edited';
		}
		var thisUniqueID = Date.now();
		var appendedEl = '\
						<div class="col-sm-5 mb-1 variant-' + thisUniqueID + '">\
							<input type="hidden" class="' + fieldsPref + 'VariantId" value="' + thisUniqueID + '">\
							<input type="text" class="form-control ' + fieldsPref + 'VariantTitle">\
						</div>\
						<div class="col-sm-6 mb-1 variant-' + thisUniqueID + '">\
							<select class="form-control ' + fieldsPref + 'VariantValue">\
								<option value="0">Назначить позже</option>\
								<option value="-1">Тупиковая ветка</option>\
								<option value="-2">Успешное завершение</option>';
		$.each(linkedQuestions, function ( linkedIndex, linkedValue ) {
			appendedEl = appendedEl + '\
								<option value="' + linkedIndex + '">' + linkedValue + '</option>';
		});
		appendedEl = appendedEl + '\
							</select>\
						</div>\
						<div class="col-sm-1 mb-1 variant-' + thisUniqueID + '">\
							<input type="button" class="btn btn-sm btn-outline-danger remove-variant" data-variant-id="' + thisUniqueID + '" value="X">\
						</div>';
		if($('#mainContainer').attr('data-last-action') == "edit"){
			$('#editQuestionVariants').append(appendedEl);
		}else{
			$('#addQuestionVariants').append(appendedEl);
		}
	});
	$(document.body).on('click', '.edit-question-save', function(e) {
		e.preventDefault();
		var editedVariantIds = [];
		var editedVariantTitles = [];
		var editedVariantValues = [];
		$.each($('.editedVariantId'), function ( variantIdIndex, variantIdValue ) {
			editedVariantIds.push($(variantIdValue).val());
		});
		$.each($('.editedVariantTitle'), function ( variantTitleIndex, variantTitleValue ) {
			editedVariantTitles.push($(variantTitleValue).val());
		});
		$.each($('.editedVariantValue'), function ( variantVarIndex, variantVarValue ) {
			editedVariantValues.push($(variantVarValue).val());
		});
		var editedDataObj = {
			id:				$(this).attr('data-id'),
			question_text: 	$('#editQuestionText').val(),
			variant_ids: 	editedVariantIds,
			variant_titles: editedVariantTitles,
			variant_links:  editedVariantValues
		};
		$.post(
			'/management/update_question', 
			editedDataObj, 
			function(response) {
				console.log(response);
			}
		);
	});
	$(document.body).on('click', '.add-question-save', function(e) {
		e.preventDefault();
		var createdVariantIds = [];
		var createdVariantTitles = [];
		var createdVariantValues = [];
		$.each($('.createdVariantId'), function ( variantIdIndex, variantIdValue ) {
			createdVariantIds.push($(variantIdValue).val());
		});
		$.each($('.createdVariantTitle'), function ( variantTitleIndex, variantTitleValue ) {
			createdVariantTitles.push($(variantTitleValue).val());
		});
		$.each($('.createdVariantValue'), function ( variantVarIndex, variantVarValue ) {
			createdVariantValues.push($(variantVarValue).val());
		});
		var createdDataObj = {
			question_text: 	$('#addQuestionText').val(),
			parent_id:      $(this).attr('data-parent'),
			type:  			2,
			topic:  		$(this).attr('data-topic'), 
			variant_ids: 	createdVariantIds,
			variant_titles: createdVariantTitles,
			variant_links:  createdVariantValues
		};
		$.post(
			'/management/create_question', 
			createdDataObj, 
			function(response) {
				console.log(response);
			}
		);
	});
</script>
@endsection