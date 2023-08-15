<?php 
    include 'php/connection.php';
    include 'php/controlador.php';

    if (!isset($_SESSION['user_token'])) {
        header('Location: filtered.php');
    }

    $sql = "SELECT * FROM places WHERE id = '{$_GET['place']}'";
    $run = mysqli_query($connection, $sql);
    $dataPlace = mysqli_fetch_array($run);

    $sql2 = "SELECT * FROM user_ratings WHERE user_id = '{$_SESSION['user_id']}' AND place_id = '{$_GET['place']}'";
    $run2 = mysqli_query($connection, $sql2);

    if (mysqli_num_rows($run2) > 0) {
        $ratingValue = 1;
    }else if(mysqli_num_rows($run2) == 0){
        $ratingValue = 0;
    }

    if (!isset($_SESSION['user_id'])) {
        echo "no";
    }

    if (isset($_SESSION['isLogin'])) {
        $sql = "SELECT * FROM users WHERE token = '{$_SESSION['user_token']}'";
        $run = mysqli_query($connection, $sql);
        $dataUser = mysqli_fetch_array($run);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Google Fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300&display=swap" rel="stylesheet">
    <!--Frameworks-->
    <script src="https://cdn.jsdelivr.net/npm/kute.js@2.1.2/dist/kute.min.js"></script>
    <!--FontAwesome-->
    <script src="https://kit.fontawesome.com/61fb4717c0.js" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<style><?php include 'css/place.css'?></style>
<body>
    <header class="header">
        <div class="header__logo"><img src="img/Logo SivarTour BN web.png"></div>
        <div class="options__menu">
            <div class="option notifications">
                <div class="option__title">
                    <i class="fa-regular fa-bell"></i>
                    <h3>Notificaciones</h3>
                    <i class="fa-solid fa-chevron-down hideNotis"></i>
                </div>
                <div class="option__info__container hide">
                    <div class="option__info">
                        <i class="fa-solid fa-xmark close"></i>
                        <h4>Publicación rechazada</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi maiores ipsum dolorum.</p>
                    </div>
                    <div class="option__info">
                        <i class="fa-solid fa-xmark close"></i>
                        <h4>Publicación aceptada</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi maiores ipsum dolorum.</p>
                    </div>
                </div>
            </div>
            <div class="option profile">
                <i class="fa-regular fa-circle-user"></i>
                <h3>Mi perfil</h3>
            </div>
            <div class="option favorites">
                <i class="fa-regular fa-star"></i>
                <h3>Mis favoritos</h3>
            </div>
            <div class="option translate">
                <input type="checkbox" id="cambiar">
                <label for="cambiar">aquí se cambia el idioma</label>
            </div>
        </div>
        <div class="header__options">
            <!--<a href="profile.php" class="profile"><i class="fa-regular fa-circle-user"></i></a>-->
            <!--<i class="fa-solid fa-globe"></i>-->
            <i class="fa-solid fa-bars-staggered showMenu"></i>
        </div>
    </header>
    <div class="place__hero">
        <div class="place__hero__text">
            <h1><?php echo $dataPlace['name'] ?></h1>
            <p><?php echo $dataPlace['description'] ?></p>
            <form method="post" class="hero__text__button">
                <i class="fa-solid fa-magnifying-glass" onclick="googleFind()"></i>
                <i class="fa-brands fa-waze" onclick="abrirWaze()"></i>
                <?php
                    $filledStar = $ratingValue == 1 ? 'fa-solid' : 'fa-regular';
                    echo '<i class="fa-star ' . $filledStar . ' rating" data-rating="1"></i>';
                ?>
                <i class="fa-regular fa-rectangle-list collection"></i>
            </form>
        </div>
        <div class="place__hero__img">
            <?php echo '<img class="hero__img" src="'.$dataPlace["img1"].'">'?>
            <?php echo '<img class="hero__img" src="'.$dataPlace["img2"].'">'?>
            <?php echo '<img class="hero__img one" src="'.$dataPlace["img3"].'">'?>
        </div>
    </div>
    <div class="place__body">
        <div class="place__body__left">
            <div class="body__left__map" id="map">
            </div>
            <div class="body__left__review">
                <form method="post" class="review__comment">
                    <input type="text" name="comment" id="" placeholder="Escribe tu comentario">
                    <input type="submit" value="" name="newComment">
                </form>

                <div class="slider">
                    <?php
                        $query = "SELECT * FROM comments INNER JOIN users ON comments.id_user = users.id WHERE comments.id_place = '{$_GET['place']}'";
                        $resultado = mysqli_query($connection, $query);

                        $x = 0;

                        while ($data = mysqli_fetch_assoc($resultado)) {
                            $claseActiva = ($x === 0) ? 'active' : '';
                            ?>
                            <div class="slider__container <?php echo $claseActiva?>">
                                <form method="post" class="slider__user">
                                    <div class="slider__user__img"><?php echo substr($data['name'], 0, 1); ?></div>
                                    <input type="hidden" name="idUserComment" value="<?php echo $data['id']; ?>">
                                    <button type="submit" name="userComment" class="userProfile"><?php echo $data['name']; ?></button>
                                    <div class="user__info">
                                        <h3>Maestro viajero</h3>
                                        <div class="info__points">
                                            <i class="fa-solid fa-heart"></i>
                                            <p>4.96</p>
                                        </div>
                                    </div>
                                </form>
                                <div class="slider__text">
                                    <p><?php echo $data['comment']; ?></p>
                                </div>
                            </div>
                            <?php
                            $x++;
                        }

                        if (mysqli_num_rows($resultado) == 0) {
                            echo "<h3 class='noDataC'>No hay comentarios por mostrar :c</h3>";
                        }
                    ?>
                </div>
                <?php
                    if (mysqli_num_rows($resultado) != 0) {
                        ?>
                        <nav class="slider-nav">
                            <ul>
                                <li class="arrow">
                                    <button class="previous"><span><i class="fa-sharp fa-solid fa-arrow-left"></i></span></button>
                                </li>
                                <li class="arrow">
                                    <button class="next"><span><i class="fa-sharp fa-solid fa-arrow-right"></i></span></button>
                                </li>
                            </ul>
                        </nav>
                        <?php
                    }
                ?>
            </div>
            <div class="body__left__route">
                <a href="routes.html"><div class="route">
                    <i class="fa-solid fa-route"></i>
                    <h2>Agregar a la ruta</h2>
                </div></a>
            </div>
        </div>
        <div class="line"></div>
        <div class="place__body__right">
            <?php
                $sqll = "SELECT * FROM restaurants WHERE id_place = {$_GET['place']}";
                $runn = mysqli_query($connection, $sqll);

                while ($row = mysqli_fetch_array($runn)){
                    ?>
                        <div class="body__restaurant">
                            <div class="circle"></div>
                            <?php echo '<img src="'.$row['img'].'">'?>
                            <h2><?php echo $row['name'] ?></h2>
                        </div>
                    <?php
                }

                if (mysqli_num_rows($runn) == 0) {
                    echo "<h3 class='noData'>No hay restaurantes por mostrar :c</h3>";
                }
            ?>
            
            <?php
                if ($dataUser['verified'] == 1) {
                    echo '<i class="fa-solid fa-utensils restaurants"></i>';
                }
            ?>
        </div>
    </div>
    <div class="popup__container">
        <div class="popup">
            <i class="fa-solid fa-xmark closePop"></i>
            <h2>Agrega <?php echo $dataPlace['name']; ?> a tus colecciones</h2>
            <form method="post" class="collections__container">
            <?php
                $sql = "SELECT collections.id, collections.name, collections.id_user, collections_places.id_place, collections_places.id_collection FROM collections INNER JOIN collections_places ON collections.id = collections_places.id_collection WHERE collections_places.id_place != {$_GET['place']} AND collections.id_user = '{$_SESSION['user_id']}'";
                $run = mysqli_query($connection, $sql);

                while ($dataCollec = mysqli_fetch_assoc($run)) {
                    ?>
                        <button type="submit" name="collection" class="collections" value="<?php echo $dataCollec['id']; ?>"><?php echo $dataCollec['name']; ?></button>
                    <?php
                }
            ?>
            </form>
            <form method="post" class="popup__field">
                <input type="text" name="name" spellcheck="false"> 
                <label class="label">Nombre de la colección</label>
                <input type="submit" name="newCollection" value="Añadir">
            </form>
            <i class="fa-solid fa-plus new"></i>
        </div>
    </div>
    <div class="newplace__container">
        <form method="post" id="newPlaceForm" class="newplace" enctype="multipart/form-data">
            <i class="fa-solid fa-xmark close__newplace"></i>
            <div class="newplace__img">
                <input type="file" name="image" id="upload-button" required accept="image/*" />
                <label for="upload-button">
                    <i class="fa-solid fa-upload"></i>&nbsp; Fotografía
                </label>
                <p>Recuerda que el total de fotos es 1</p>
                <div id="error"></div>
                <div id="image-display"></div>
            </div>
            <div class="newplace__info">
                <div class="info__title">
                    <h1>Publica un restaurante!</h1>
                    <p>Sube una foto y escribe el nombre de algún restaurante en <?php echo $dataPlace['name'];?>.</p>
                </div>
                <div class="input__field">
                    <input type="text" name="name" required spellcheck="false"> 
                    <label>Nombre</label>
                </div>
                <input type="hidden" name="placeId" value="<?php echo $dataPlace['id'];?>">
                <input type="hidden" name="placeName" value="<?php echo $dataPlace['name'];?>">
                <input type="submit" value="Enviar" name="newRestaurant">
            </div>
        </form>
    </div>
    <?php
        if ($notice != "") {
            ?>
            <div class="notice" id="notification">
                <p><?php echo $notice; ?></p>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const notice = document.getElementById('notification');
                    
                    // Mostrar la notificación después de un retraso
                    setTimeout(function () {
                        notice.classList.add('active');
                    }, 10);

                    // Ocultar la notificación después de 3 segundos
                    setTimeout(function () {
                        notice.classList.remove('active');
                        window.history.replaceState(null,null,window.location.href);
                    }, 3000);
                });
            </script>
            <?php
        }
    ?>
    



    <script>
        document.querySelector('.restaurants').addEventListener('click', function(){
            document.querySelector('.newplace__container').classList.remove('close');
            document.querySelector('.newplace__container').classList.add('see');
        })

        document.querySelector('.close__newplace').addEventListener('click', function(){
            document.querySelector('.newplace__container').classList.remove('see');
            document.querySelector('.newplace__container').classList.add('close');
        })
    </script>

    <script>
        // Obtener la estrella
        const star = document.querySelector('.rating');

        // Asignar un evento de clic a la estrella
        star.addEventListener('click', function() {
            // Obtener el valor de la calificación de la estrella seleccionada
            const ratingValue = this.getAttribute('data-rating');

            // Enviar el valor de la calificación al backend mediante el formulario
            const form = document.createElement('form');
            form.method = 'post';
            form.action = window.location.href; // La página actual

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'rating';
            input.value = ratingValue;

            form.appendChild(input);
            document.body.appendChild(form);

            form.submit(); // Enviar el formulario para actualizar la calificación
        });
    </script>

    <script>
        document.querySelector('.collection').addEventListener('click', function(){
            document.querySelector('.popup__container').classList.add('visible');
        })

        document.querySelector('.closePop').addEventListener('click', function(){
            document.querySelector('.popup__container').classList.remove('visible');
        });

        document.querySelector('.new').addEventListener('click', function(){
            document.querySelector('.popup__field').classList.add('visiblew');
            document.querySelectorAll('.collections').forEach(function(elemento) {
                elemento.classList.add('invisiblew');
            });
            document.querySelector('.new').classList.add('invisiblew');
        });

        document.querySelector('.rating').addEventListener('click', function(){
            document.querySelector('.rating').classList.toggle('fa-regular');
            document.querySelector('.rating').classList.toggle('fa-solid');
        })
    </script>


    <script>
        document.querySelector('.closeee').addEventListener('click', function(){
            document.querySelector('.notice__container').classList.add('invisiblee');
            window.history.replaceState(null,null,window.location.href);
        });
    </script>

    <script>
        document.querySelector('.header__logo').addEventListener('click', function(){
            location.href = 'index.php';
        })

        document.querySelector('.profile').addEventListener('click', function(e){
            location.href = 'profile.php';
        });

        document.querySelector('.favorites').addEventListener('click', function(e){
            location.href = 'collections.php?favorites';
        });

        document.querySelector('.showMenu').addEventListener('click', function(e){
            document.querySelector('.options__menu').classList.toggle('show');
            document.querySelector('.showMenu').classList.toggle('black');
        });

        document.querySelector('.hideNotis').addEventListener('click', function(e){
            document.querySelector('.option__info__container').classList.toggle('hide');
            document.querySelector('.hideNotis').classList.toggle('fa-chevron-down');
            document.querySelector('.hideNotis').classList.toggle('fa-chevron-right');
        });
    </script>

    <script>
    function initMap() {
        // Obtener la dirección formateada y el nombre del lugar almacenados en PHP en $dataPlace
        var location = '<?php echo $dataPlace['location']; ?>';
        var placeName = '<?php echo $dataPlace['direction']; ?>';

        // Crear un objeto Geocoder de Google Maps para obtener más información sobre el lugar
        var geocoder = new google.maps.Geocoder();

        // Obtener la información del lugar usando el Geocoder
        geocoder.geocode({ 'address': location }, function(results, status) {
        if (status === 'OK' && results[0]) {
            // Obtener el lugar detallado
            var place = results[0];

            // Obtener las coordenadas del lugar
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();

            // Construir la URL del iframe con el nombre del lugar
            var iframeUrl = 'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(placeName) + '&zoom=12&key=AIzaSyDi-1W4L7N7-cIt4IClUTcZcJXTlHsdUGU';

            // Crear el elemento iframe
            var iframe = document.createElement('iframe');
            iframe.setAttribute('src', iframeUrl);
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('style', 'border:0;');
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('loading', 'lazy');
            iframe.setAttribute('referrerpolicy', 'no-referrer-when-downgrade');

            // Agregar el iframe al elemento div con el ID 'map'
            var mapContainer = document.getElementById('map');
            mapContainer.appendChild(iframe);
        } else {
            // Manejar el caso de error si no se pueden obtener las coordenadas o información del lugar
            console.error('Error al obtener la información del lugar.');
        }
        });
    }
    </script>

    <script>
        function abrirWaze() {
            var nombreLugar = '<?= $dataPlace['direction'] ?>' // Cambia esto al nombre del lugar que deseas
            var apiKey = "AIzaSyDi-1W4L7N7-cIt4IClUTcZcJXTlHsdUGU"; // Cambia esto a tu clave de API de Google Maps
            
            // Obtiene la ubicación actual del usuario
            navigator.geolocation.getCurrentPosition(function(position) {
                var latitudActual = position.coords.latitude;
                var longitudActual = position.coords.longitude;

                // Realiza una solicitud a la API de Geocodificación para obtener las coordenadas del lugar
                fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${nombreLugar}&key=${apiKey}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.results.length > 0) {
                            var location = data.results[0].geometry.location;
                            var latitudDestino = location.lat;
                            var longitudDestino = location.lng;
                            
                            // Construye la URL de Waze con las coordenadas de origen y destino
                            var url = `https://waze.com/ul?ll=${latitudDestino},${longitudDestino}&navigate=yes&fromlat=${latitudActual}&fromlon=${longitudActual}&from=ll.${latitudActual}%2C${longitudActual}`;
                            
                            // Abre la URL en una nueva ventana o pestaña
                            window.open(url, '_blank');
                        } else {
                            console.log("No se encontraron coordenadas para el lugar especificado.");
                        }
                    })
                    .catch(error => console.error("Error al obtener las coordenadas:", error));
            });
        }
    </script>

    <script>
        function googleFind(){
            const palabra = '<?= $dataPlace['name'] ?>'
            if (palabra) {
                const url = `https://www.google.com/search?q=${encodeURIComponent(palabra)}`;
                window.open(url, "_blank");
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDi-1W4L7N7-cIt4IClUTcZcJXTlHsdUGU&callback=initMap" async defer></script>
</body>
<script src="js/reviews.js"></script>
<script src="js/newRestaurant.js"></script>
</html>