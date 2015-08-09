@extends('api::master')

@section('content')
    <div class="container">
        <div class="row">
            <h1>{{$document->api->getName()}} <small>API</small></h1>
        </div>
        <div class="row">
            <h2>{{studly_case($document->resource->getName())}} <small>Resource</small></h2>
            <table class="table">
                <thead>
                    <tr>
                        <th width="20%">Attributes</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($document->resource->getAttributes() as $attr => $type)
                <tr>
                    <td>{{$attr}}</td>
                    <td>{{$type}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @foreach($document->http->gets() as $uri => $get)
        <div class="row">
            <h2>{{$get->value}} <small>HTTP</small></h2>

            <h3>Examples</h3>
            @foreach($get->children as $example)
            <div class="panel panel-default">
                <div class="panel-heading"> <div class="panel-title">{{$example->value}}</div></div>
                <div class="panel-body">
                    <h4>Response</h4>
                    <table class="table">
                        @foreach($example->get('Response') as $attr => $value)
                            <tr>
                                <td>{{$attr}}</td>
                                @if($attr != "Body")
                                    <td>{{is_string($value) ? $value : ""}}</td>
                                @else
                                    <td><pre><code><?php
                                        $json = json_decode($value);
                                        echo json_encode($json, JSON_PRETTY_PRINT);
                                    ?></code></pre></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
@stop