<script>

	var name = encodeURI("{{ $res['name'] }}");

	var api = encodeURI("{{ $res['url'] }}");

	var Url = 'https://votexue.xingkong.us/#/?vid={{ $res['id'] }}&name=' + name +'&u=' + api;

	window.location.href = encodeURI(Url);

</script>
