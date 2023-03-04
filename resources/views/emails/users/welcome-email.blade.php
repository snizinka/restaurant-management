<x-mail::message>
# Welcome!

Glad to see you joined us!

<x-mail::button :url="'http://127.0.0.1:8000/api/emai/verify/'.$data">
Submit email
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
