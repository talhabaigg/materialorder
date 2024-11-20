<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enable Notifications</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>
</head>
<body>
    <div class="container">
        <h1>Notification Setup</h1>
        <button id="enable-notifications" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            Enable Notifications
        </button>
    </div>

    <script>
        // Firebase configuration
        // const firebaseConfig = {
        //     apiKey: "AIzaSyDVyiFmcddqmtV4obWNHPBG4tNuVTrY5Po",
        //     authDomain: "push-notification-e7b2e.firebaseapp.com",
        //     projectId: "push-notification-e7b2e",
        //     storageBucket: "push-notification-e7b2e.firebasestorage.app",
        //     messagingSenderId: "349022740315",
        //     appId: "1:349022740315:web:75bbe62bb61a00a8b5c47e",
        // };
        const firebaseConfig = {
            apiKey: "{{ $firebaseConfig['api_key'] }}",
            authDomain: "{{ $firebaseConfig['auth_domain'] }}",
            projectId: "{{ $firebaseConfig['project_id'] }}",
            storageBucket: "{{ $firebaseConfig['storage_bucket'] }}",
            messagingSenderId: "{{ $firebaseConfig['messaging_sender_id'] }}",
            appId: "{{ $firebaseConfig['app_id'] }}",
            
         };
        // Initialize Firebase
        const app = firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // Request permission and get token
        async function generateFcmToken() {
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    const token = await messaging.getToken({
                        vapidKey: "{{ $vapidKey['public_key'] }}",
                    });
                    if (token) {
                        console.log('FCM Token:', token);
                        sendTokenToServer(token);
                    } else {
                        console.error('Failed to generate FCM token');
                    }
                } else {
                    console.error('Notification permission denied');
                }
            } catch (error) {
                console.error('Error generating FCM token:', error);
            }
        }

        // Send token to server
        async function sendTokenToServer(token) {
            try {
                const response = await fetch('/save-fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ fcm_token: token }),
                });

                if (response.ok) {
                    console.log(response);
                } else {
                    console.error('Failed to save token');
                }
            } catch (error) {
                console.error('Error saving token:', error);
            }
        }

        // Attach event listener to button
        document.getElementById('enable-notifications').addEventListener('click', generateFcmToken);
    </script>
</body>
</html>
