@extends('layouts.app')
@section('styles')
<style>
    .chat-container{
        display: flex;
        flex-direction: column;
    }
    .chat{
        border: 1px solid gray;
        border-radius: 3px;
        width: 50%;
        padding: 0.5rem;
    }
    .chat-left{
        background-color: white;
        align-self: flex-start;
    }
    .chat-right{
        background-color: #adff2f;
        align-self: flex-end;
    }

    .message-container{
        padding: 0.5rem;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Conversation') }}</div>

                <div class="card-body">
                    <div class="chat-container">
                        @forelse ($chats as $index=>$chat)
                           @if ($chat->sender_id == Auth::user()->id)
                                <p class="chat chat-left"><b>{{ $chat->sender_name }}</b><br> {{ $chat->message }}</p>
                           @else
                                <p class="chat chat-right"><b>{{ $chat->sender_name }}</b><br>{{ $chat->message }}</p>
                           @endif
                        @empty
                           <p class="text-center text-danger">No Conversation Found!!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card message-container">
                <form action="{{ url('save-chat') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="message">Write Message</label>
                        <textarea name="message" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                    <button class="btn btn-primary float-right">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>

    const messaging = firebase.messaging();
    // Add the public key generated from the console here.
    messaging.getToken({vapidKey: "BBcPWp-d9TSId5Awihx9-N4RCi_09vjr1UxpYBn_iM0ZzSqpe1Z-Hnp0VMhkXdSVn35MDSCjXRRq107c7iT6OJA"});

    function sendTokenToServer(fcm_token){
        //console.log('token retrieved', token);
        const user_id = {{ Auth::user()->id }};
        //console.log(user_id);

        axios.post('/api/save-token', {
            fcm_token, user_id
        })
        .then(function (response) {
            console.log(response);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function retrieveToken() {
        // Get registration token. Initially this makes a network call, once retrieved
        // subsequent calls to getToken will return from cache.
        messaging.getToken({ vapidKey: 'BBcPWp-d9TSId5Awihx9-N4RCi_09vjr1UxpYBn_iM0ZzSqpe1Z-Hnp0VMhkXdSVn35MDSCjXRRq107c7iT6OJA' }).then((currentToken) => {
        if (currentToken) {
            // Send the token to your server and update the UI if necessary
            sendTokenToServer(currentToken);
        } else {
            // Show permission request UI
            console.log('No registration token available. Request permission to generate one.');
            alert('You must have to permit notification allow access!');
        }
        }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
        // ...
        });
    }

    retrieveToken();

    messaging.onTokenRefresh(() => {
        retrieveToken();
    });

    messaging.onMessage((payload)=> {
        console.log('Message Recieved');
        console.log(payload);
        location.reload();
    });

</script>
@endsection
