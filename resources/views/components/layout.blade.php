<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite('resources/js/app.js')
</head>
<body class="antialiased">
    {{ $slot }}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('optimal', () => ({
                Charisma: '??',
                Intelligence: '??',
                Memory: '??',
                Perception: '??',
                Willpower: '??',

                optimize()  {
                    axios.post('/optimize')
                        .then(response => {
                            this.Charisma = response.data.Charisma;
                            this.Intelligence = response.data.Intelligence;
                            this.Memory = response.data.Memory;
                            this.Perception = response.data.Perception;
                            this.Willpower = response.data.Willpower;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            }))
        });
    </script>
</body>
</html>
