@extends('layouts.app')

@section('content')
<div class="container" id="mainContainer" data-call="">
	<div class="row">
		<div class="col-12 topics-list mb-3 d-flex justify-content-center">
	@foreach($topics as $thisTopic)
			<button 
				type="button" 
				class="btn btn-secondary topic-selector" 
				id="topic{{ $thisTopic->id }}" 
				data-id="{{ $thisTopic->id }}"
				data-parent="-1"
				style="margin-right:5px;">{{ $thisTopic->topic_name }}</button>
	@endforeach
			<button type="button" class="btn btn-light clear-all" style="margin-right:5px;">Очистить диалог</button>
		</div>
		<div class="col-12 questionnaire"></div>
		<div class="col-12" style="height:100px;" id="questionnaireBottom"></div>
	</div>
</div>
<script>
	var d = new Date();
	$('#mainContainer').attr('data-call', d.getTime());
	$.ajaxSetup(
		{
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		}
	);
	$('.topic-selector').click(function() {
		$('#mainContainer').attr('data-topic', $(this).attr('data-id'));
		$.post(
			'/questionnaire', 
			{ 
				topic: 		$(this).attr('data-id'),
				parent_id: 	$(this).attr('data-parent'),
				call_id: 	$('#mainContainer').attr('data-call'), 
			}, 
			function(response) {
				console.log(response);
				if(response.has_response){
					var variantButtons = '';
					if(response.variants !== null){
						for(var thisVariant=0; thisVariant<response.variants.length; thisVariant++){
							variantButtons = variantButtons + '<button \
				type="button" \
				class="btn btn-secondary question-answer" \
				id="topic' + $('#mainContainer').attr('data-topic') + '" \
				data-id="' + response.variants[thisVariant].link + '" \
				data-parent="' + response.id + '" \
				data-button="' + response.variants[thisVariant].id + '" \
				style="margin-right:5px;">' + response.variants[thisVariant].title + '</button>'
						}
					}
					$('.questionnaire').append('\
		<div class="col-12 mb-3">\
			<div class="card">\
				<div class="card-body">\
					' + response.question_text + '\
				</div>\
				<div class="card-footer">\
					' + variantButtons + '\
				</div>\
			</div>\
		</div>');
				}else{
					$('.questionnaire').append('\
		<div class="col-12 mb-3">\
			<div class="alert alert-warning">Нет продолжения для данного диалога.</div>\
		</div>');
				}
			},
			"json"
		);
	});
	$('.clear-all').click( function () {
		$('.questionnaire').empty();
		$('#mainContainer').attr('data-call', d.getTime());
	});
	$(document).on('click', '.question-answer' , function() {
		var isDialogueFinished = false;
		if($(this).attr('data-id') == -1){
			$('.questionnaire').append('\
		<div class="col-12 mb-3">\
			<div class="alert alert-danger">Неуспешное завершение разговора.</div>\
		</div>');
			isDialogueFinished = true;
		}else if($(this).attr('data-id') == -2){
			$('.questionnaire').append('\
		<div class="col-12 mb-3">\
			<div class="alert alert-success">Успешное завершение разговора, поздравляем.</div>\
		</div>');
			isDialogueFinished = true;
		}
		$.post(
			'/questionnaire', 
			{ 
				topic: 		$('#mainContainer').attr('data-topic'),
				parent_id: 	$(this).attr('data-parent'),
				answered: 	$(this).attr('data-button'), 
				next_id: 	$(this).attr('data-id'),
				call_id: 	$('#mainContainer').attr('data-call'), 
			}, 
			function(response) {
				console.log(response);
				if(response.has_response){
					var variantButtons = '';
					if(response.variants !== null){
						for(var thisVariant=0; thisVariant<response.variants.length; thisVariant++){
							variantButtons = variantButtons + '<button \
				type="button" \
				class="btn btn-secondary question-answer" \
				id="topic' + $('#mainContainer').attr('data-topic') + '" \
				data-id="' + response.variants[thisVariant].link + '" \
				data-parent="' + response.id + '" \
				data-button="' + response.variants[thisVariant].id + '" \
				style="margin-right:5px;">' + response.variants[thisVariant].title + '</button>'
						}
					}
					$('.questionnaire').append('\
		<div class="col-12 mb-3">\
			<div class="card">\
				<div class="card-body">\
					' + response.question_text + '\
				</div>\
				<div class="card-footer">\
					' + variantButtons + '\
				</div>\
			</div>\
		</div>');
				}else{
					if(isDialogueFinished == false){
						$('.questionnaire').append('\
		<div class="col-12 mb-3">\
			<div class="alert alert-warning">Нет продолжения для данного диалога.</div>\
		</div>');
					}
				}
			},
			'json'
		);
		$('html, body').animate({scrollTop: $('#questionnaireBottom').height()+ 7200 },"slow");
	});
</script>
@endsection