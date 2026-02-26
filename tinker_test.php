$req = \Illuminate\Http\Request::create('/test-health-ingest', 'GET');
$res = app()->handle($req);
echo $res->getContent();