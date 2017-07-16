<script type="text/javascript">
			// var j = jQuery.noConflict();
			$(document).ready(function(){

				/* Переменная-флаг для отслеживания того, происходит ли в данный момент ajax-запрос. В самом начале даем ей значение false, т.е. запрос не в процессе выполнения */
				var inProgress = false;
				/* С какой статьи надо делать выборку из базы при ajax-запросе */
				var startFrom = 18;

				/* Используйте вариант $('#more').click(function() для того, чтобы дать пользователю возможность управлять процессом, кликая по кнопке "Дальше" под блоком статей (см. файл index.php) */
					// $(window).scroll(function() {
						$('#more').click(function(){
							$.post("/ajax/getnextrows/",{
						         // Параметр передаваемый в скрипт
						         startFrom: startFrom,
						         category_id: "<?=$category['id']?>"
						     },function(data) {
						        data = jQuery.parseJSON(data);
						        alert('lenth: '+data.length);
						        if (data.length > 0) {
						        	$.each(data, function(index, data){
						        		//alert(data);
						            	/* Отбираем по идентификатору блок со статьями и дозаполняем его новыми данными */
						            	$("#articles").append(data);
						            	startFrom++;
						            });
						            
						        }
						     });
							});
			});

</script>