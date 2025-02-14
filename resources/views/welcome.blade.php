<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Black Sphere with Triangular Mesh</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background-color: white;
        }

        canvas {
            display: block;
        }
    </style>
</head>

<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Scene, Camera, Renderer
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({
            antialias: true
        });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // Sphere Geometry with Wireframe
        const geometry = new THREE.SphereGeometry(2, 32, 32);
        const material = new THREE.MeshBasicMaterial({
            color: 0x000000
        });
        const sphere = new THREE.Mesh(geometry, material);
        scene.add(sphere);

        // Wireframe
        const wireframe = new THREE.WireframeGeometry(geometry);
        const lineMaterial = new THREE.LineBasicMaterial({
            color: 0xffffff,
            linewidth: 2
        });
        const wireframeMesh = new THREE.LineSegments(wireframe, lineMaterial);
        sphere.add(wireframeMesh);

        // Position Camera
        camera.position.z = 5;

        // Animation
        function animate() {
            requestAnimationFrame(animate);
            sphere.rotation.y += 0.01;
            sphere.rotation.x += 0.005;
            renderer.render(scene, camera);
        }

        // Resize Handling
        window.addEventListener('resize', () => {
            renderer.setSize(window.innerWidth, window.innerHeight);
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
        });

        animate();
    </script>
</body>

</html>
