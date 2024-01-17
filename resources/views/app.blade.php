<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>
    Sistem Informasi Tracking Excel
  </title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <link rel="stylesheet" href="{{ asset('vendor/stisla/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/stisla/css/components.css') }}">
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      @include('components.topbar')
      @include('components.sidebar')

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Sistem Informasi Tracking Excel</h1>
            <div class="section-header-breadcrumb">
                @foreach ($section_headers as $section_header)
                    <div class="breadcrumb-item">
                        @if($section_header['is_active'])
                            {{ $section_header['menu'] }}
                        @else
                            <a href="{{ $section_header['href'] }}">
                                {{ $section_header['menu'] }}
                            </a>
                        @endif
                        
                    </div>
                @endforeach
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">{{ $section_title }}</h2>
            <p class="section-lead">{{ $section_lead }}</p>
            @yield('content')
          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2023-2024 <div class="bullet"></div> Developed By <a href="https://www.instagram.com/syaokifaradisa09" target="_blank">Muhammad Syaoki Faradisa</a>
        </div>
        <div class="footer-right">
          2.1.0
        </div>
      </footer>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  
  <script src="{{ asset('vendor/stisla/js/stisla.js') }}"></script>
  <script src="{{ asset('vendor/stisla/js/scripts.js') }}"></script>

  @yield('js-extends')
</body>
</html>
