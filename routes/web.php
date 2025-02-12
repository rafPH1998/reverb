<?php

use App\Models\User;
use App\Notifications\MessageTestNotification;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
Fluxo do Laravel Reverb com Notificações:

1. Quando uma notificação é enviada (via $user->notify()), o seguinte processo ocorre:
   - A notificação é criada com os dados do payload
   - Por implementar ShouldBroadcast, ela é automaticamente enviada para a fila (queue)

2. O sistema de broadcast então:
   - Processa a notificação através de um job em background
   - Utiliza o driver configurado (neste caso Reverb) para transmissão
   - Envia para o servidor WebSocket do Reverb

3. O Reverb:
   - Mantém conexões WebSocket ativas com os clientes
   - Gerencia canais privados e públicos
   - Autentica usuários através do Laravel
   - Distribui as mensagens para os clientes conectados no canal correto

4. No cliente:
   - O Laravel Echo escuta o canal específico do usuário
   - Recebe a notificação em tempo real via WebSocket
   - Permite tratamento da mensagem no frontend

Obs: O broadcast acontece apenas para usuários autenticados e conectados
ao canal correto, garantindo a segurança da comunicação.
*/
Route::get('notification-test', function () {
    $data = [
        'status'    => 'success',
        'body'      => 'Mensagem padrão notificação para teste',
        'timestamp' => now()->toISOString(),
        'type'      => 'test-notification'
    ];

    try {
        $user = User::query()->firstOrFail();
        $user->notify(new MessageTestNotification($data));

        return response()->json([
            'message' => 'Notificação enviada com sucesso!',
            'status' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro ao enviar notificação',
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::get('login/{id}', function (int $id) {
    return auth()->loginUsingId($id);
});

Route::get('/debug-reverb', function () {
    return response()->json([
        'REVERB_HOST' => env('REVERB_HOST'),
        'REVERB_PORT' => env('REVERB_PORT'),
        'REVERB_SCHEME' => env('REVERB_SCHEME'),
    ]);
});
