<style>
    @import url("https://fonts.googleapis.com/css?family=Fira+Code&display=swap");

    * {
        margin: 0;
        padding: 0;
        font-family: "Helvetica";
    }

    body {
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #b1cce6;
    }

    .container {
        text-align: center;
        margin: auto;
        padding: 3em;

        img {
            width: 256px;
            height: 225px;
        }

        h1 {
            margin-top: 1rem;
            font-size: 35px;
            text-align: center;

            span {
                font-size: 60px;
            }
        }

        p {
            margin-top: 1rem;
        }

        p.info {
            margin-top: 6em;
            font-size: 14px;

            a {
                text-decoration: none;
                color: rgb(84, 84, 206);
            }
        }
    }
</style>

<div class="container">
    <img src="https://i.imgur.com/qIufhof.png" />

    <h1>
        <span>@yield('code')</span> <br />
        @yield('message')
    </h1>
    <p>If you think this is an error, please contact us and we will investigate.</p>
    <p class="info">
        Click here to return to our <a href="{{ url('/') }}">home page</a>.
    </p>
</div>
