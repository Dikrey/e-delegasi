@extends('layout.main')

@push('style')
    <link rel="stylesheet" href="{{ asset('sneat/vendor/libs/apex-charts/apex-charts.css') }}"/>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <link rel="stylesheet" href="{{ asset('css/fullcalendar-theme.css') }}?v=2">
    <style>
        #agenda-calendar-admin { min-height: 560px; }
        /* ============ HERO ============ */
        .dash-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
            background: linear-gradient(120deg, #6d67e4 0%, #5448d6 45%, #1f1a66 100%);
            color: #fff;
            box-shadow: 0 18px 40px -14px rgba(109, 103, 228, 0.65);
            animation: fadeSlide 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .dash-hero::before, .dash-hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            filter: blur(6px);
            pointer-events: none;
        }
        .dash-hero::before { width: 220px; height: 220px; top: -80px; right: 8%; animation: heroBlob 9s ease-in-out infinite alternate; }
        .dash-hero::after { width: 140px; height: 140px; bottom: -60px; right: 32%; animation: heroBlob 11s ease-in-out infinite alternate-reverse; }
        @keyframes heroBlob { from { transform: translateY(0) scale(1); } to { transform: translateY(26px) scale(1.15); } }
        @keyframes fadeSlide { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

        /* ============ STAT CARDS ============ */
        .stat-card {
            position: relative;
            overflow: hidden;
            border: none !important;
            border-radius: 1rem !important;
            box-shadow: 0 10px 26px -12px rgba(38, 42, 71, 0.35);
            transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s;
            animation: fadeSlideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 22px 44px -18px rgba(38, 42, 71, 0.5); }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.35s; }
        @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(26px); } to { opacity: 1; transform: translateY(0); } }

        .stat-glyph {
            width: 52px; height: 52px; display: grid; place-items: center;
            border-radius: 14px; font-size: 24px; color: #fff;
        }
        .count-up { font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; }
        .trend-up { color: #36f1a5; font-weight: 700; }
        .trend-down { color: #ff6b6b; font-weight: 700; }

        /* ============ CHART CARDS ============ */
        .chart-card {
            border: none !important; border-radius: 1.1rem !important;
            box-shadow: 0 12px 30px -14px rgba(38, 42, 71, 0.3);
            transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s;
            animation: fadeSlideUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .chart-card:hover { box-shadow: 0 22px 44px -16px rgba(38, 42, 71, 0.45); }

        /* ============ THREE.JS SCENE ============ */
        #three-envelope {
            width: 260px;
            height: 240px;
            display: block;
            flex-shrink: 0;
            cursor: grab;
            border-radius: 1rem;
        }
        #three-envelope:active { cursor: grabbing; }

        /* ============ TODAY BREAKDOWN (Modern) ============ */
        .breakdown-card {
            border: none !important;
            border-radius: 1.25rem !important;
            overflow: hidden;
            box-shadow: 0 14px 34px -14px rgba(38,42,71,0.32);
            animation: fadeSlideUp 0.8s cubic-bezier(0.22,1,0.36,1) both;
            position: relative;
        }
        .breakdown-card::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(160deg, rgba(109,103,228,0.04) 0%, rgba(54,241,205,0.03) 50%, rgba(255,107,203,0.03) 100%);
            pointer-events: none;
        }
        .breakdown-row {
            position: relative;
            display: flex; align-items: center; gap: 1rem;
            padding: 0.85rem 1rem;
            border-radius: 14px;
            transition: background 0.22s ease, transform 0.22s ease;
        }
        .breakdown-row:hover {
            background: rgba(109,103,228,0.06);
            transform: translateX(4px);
        }
        .breakdown-row + .breakdown-row { border-top: 1px solid rgba(109,103,228,0.07); }
        .breakdown-icon {
            width: 48px; height: 48px;
            display: grid; place-items: center;
            border-radius: 14px; font-size: 22px; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 8px 18px -6px rgba(0,0,0,0.25);
        }
        .breakdown-info { flex: 1; min-width: 0; }
        .breakdown-info .label { font-size: 0.78rem; color: #7c7f96; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .breakdown-info .value { font-size: 1.65rem; font-weight: 800; letter-spacing: -0.5px; }
        .breakdown-bar { height: 6px; border-radius: 99px; background: rgba(109,103,228,0.1); overflow: hidden; width: 100%; margin-top: 6px; }
        .breakdown-bar-fill { height: 100%; border-radius: 99px; transition: width 1.4s cubic-bezier(0.22,1,0.36,1); }
        .breakdown-pct {
            font-size: 0.82rem; font-weight: 700; flex-shrink: 0;
            width: 46px; text-align: center; padding: 0.3rem 0;
            border-radius: 10px;
        }
        .breakdown-total-row {
            position: relative;
            margin-top: 0.8rem;
            padding: 0.9rem 1rem;
            background: linear-gradient(135deg, rgba(109,103,228,0.08), rgba(54,241,205,0.06));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .breakdown-total-row .total-value { font-size: 1.45rem; font-weight: 800; color: var(--surat-primary); }

        /* ============ QUICK ACTIONS ============ */
        .quick-action-card {
            border: none !important; border-radius: 1rem !important;
            box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3);
            animation: fadeSlideUp 0.85s cubic-bezier(0.22,1,0.36,1) both;
        }
        .quick-action-tile {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 0.5rem; padding: 1rem 0.5rem; border-radius: 14px;
            text-decoration: none; color: #4a4e6a; font-weight: 600; font-size: 0.78rem;
            transition: transform 0.22s cubic-bezier(0.22,1,0.36,1), background 0.22s ease, box-shadow 0.22s ease;
            cursor: pointer;
        }
        .quick-action-tile:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -10px rgba(109,103,228,0.35); background: rgba(109,103,228,0.08); color: #322f61; }
        .quick-action-icon {
            width: 46px; height: 46px; display: grid; place-items: center;
            border-radius: 14px; font-size: 22px; color: #fff;
        }

        /* ============ ACTIVITY TIMELINE ============ */
        .timeline-card {
            border: none !important; border-radius: 1rem !important;
            box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3);
            animation: fadeSlideUp 0.9s cubic-bezier(0.22,1,0.36,1) both;
        }
        .tl-item {
            display: flex; gap: 0.8rem; padding: 0.7rem 0;
            border-bottom: 1px solid rgba(109,103,228,0.07);
            transition: background 0.2s ease;
        }
        .tl-item:last-child { border-bottom: none; }
        .tl-item:hover { background: rgba(109,103,228,0.03); border-radius: 10px; margin: 0 -0.4rem; padding: 0.7rem 0.4rem; }
        .tl-dot {
            flex-shrink: 0; width: 38px; height: 38px; display: grid; place-items: center;
            border-radius: 12px; font-size: 16px; color: #fff;
        }
        .tl-content { min-width: 0; flex: 1; }
        .tl-content h6 { font-size: 0.85rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .tl-content p { font-size: 0.76rem; color: #7c7f96; margin: 2px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .tl-time { font-size: 0.68rem; color: #a0a3bd; flex-shrink: 0; white-space: nowrap; }
    </style>
@endpush

@push('script')
    <script src="{{ asset('sneat/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script>
    (function () {

        /* ========== THREE.JS 3D ENVELOPE ========== */
        (function initThreeScene () {
            var container = document.getElementById('three-envelope');
            if (!container || typeof THREE === 'undefined') return;

            var W = 260, H = 240;
            var scene = new THREE.Scene();
            var camera = new THREE.PerspectiveCamera(42, W / H, 0.1, 1000);
            camera.position.set(0, 1.2, 6.5);
            camera.lookAt(0, 0.3, 0);

            var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
            renderer.setSize(W, H);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.setClearColor(0x000000, 0);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.1;
            container.appendChild(renderer.domElement);

            /* --- Lights --- */
            var ambLight = new THREE.AmbientLight(0xc4b5fd, 0.5);
            scene.add(ambLight);

            var dirLight = new THREE.DirectionalLight(0xffffff, 1.2);
            dirLight.position.set(4, 6, 4);
            dirLight.castShadow = true;
            dirLight.shadow.mapSize.width = 512;
            dirLight.shadow.mapSize.height = 512;
            scene.add(dirLight);

            var pointLight1 = new THREE.PointLight(0x6d67e4, 0.8, 20);
            pointLight1.position.set(-3, 3, 2);
            scene.add(pointLight1);

            var pointLight2 = new THREE.PointLight(0xff6bcb, 0.5, 20);
            pointLight2.position.set(3, -1, 3);
            scene.add(pointLight2);

            var rimLight = new THREE.PointLight(0x36f1cd, 0.6, 15);
            rimLight.position.set(0, -3, -3);
            scene.add(rimLight);

            /* --- Materials --- */
            var paperMat = new THREE.MeshPhysicalMaterial({
                color: 0xf8f6ff,
                roughness: 0.25,
                metalness: 0.02,
                clearcoat: 0.3,
                clearcoatRoughness: 0.15,
                side: THREE.DoubleSide,
            });
            var flapMat = new THREE.MeshPhysicalMaterial({
                color: 0xe8e4f8,
                roughness: 0.3,
                metalness: 0.03,
                clearcoat: 0.25,
                side: THREE.DoubleSide,
            });
            var sealMat = new THREE.MeshPhysicalMaterial({
                color: 0x6d67e4,
                roughness: 0.18,
                metalness: 0.35,
                clearcoat: 0.6,
                emissive: 0x6d67e4,
                emissiveIntensity: 0.15,
            });
            var shadowMat = new THREE.MeshBasicMaterial({
                color: 0x6d67e4,
                transparent: true,
                opacity: 0.12,
            });

            /* --- Envelope body --- */
            var envGroup = new THREE.Group();
            var bw = 2.8, bh = 2.0, bd = 0.06;

            // Bottom face
            var bottomGeo = new THREE.BoxGeometry(bw, bd, bh);
            var bottomMesh = new THREE.Mesh(bottomGeo, paperMat);
            bottomMesh.position.y = 0;
            bottomMesh.castShadow = true;
            envGroup.add(bottomMesh);

            // Back wall
            var backGeo = new THREE.BoxGeometry(bw, bh, bd);
            var backMesh = new THREE.Mesh(backGeo, paperMat);
            backMesh.position.set(0, bh / 2, -bh / 2);
            backMesh.castShadow = true;
            envGroup.add(backMesh);

            // Left wall
            var sideGeo = new THREE.BoxGeometry(bd, bh, bh);
            var leftMesh = new THREE.Mesh(sideGeo, paperMat);
            leftMesh.position.set(-bw / 2, bh / 2, 0);
            leftMesh.castShadow = true;
            envGroup.add(leftMesh);

            // Right wall
            var rightMesh = new THREE.Mesh(sideGeo, paperMat);
            rightMesh.position.set(bw / 2, bh / 2, 0);
            rightMesh.castShadow = true;
            envGroup.add(rightMesh);

            // Front wall (slightly transparent)
            var frontMat = paperMat.clone();
            frontMat.transparent = true;
            frontMat.opacity = 0.35;
            var frontGeo = new THREE.BoxGeometry(bw, bh * 0.55, bd);
            var frontMesh = new THREE.Mesh(frontGeo, frontMat);
            frontMesh.position.set(0, bh * 0.275, bh / 2);
            envGroup.add(frontMesh);

            // --- Flap (triangular, opens upward) ---
            var flapShape = new THREE.Shape();
            flapShape.moveTo(-bw / 2, 0);
            flapShape.lineTo(0, -bh * 0.72);
            flapShape.lineTo(bw / 2, 0);
            flapShape.lineTo(-bw / 2, 0);
            var flapGeo = new THREE.ShapeGeometry(flapShape);
            var flapMesh = new THREE.Mesh(flapGeo, flapMat);
            flapMesh.position.set(0, bh, -bh / 2 + bd / 2);
            flapMesh.rotation.x = -0.35;
            flapMesh.castShadow = true;
            envGroup.add(flapMesh);

            // --- Inner letter (paper inside) ---
            var letterGeo = new THREE.BoxGeometry(bw * 0.72, bh * 0.48, 0.02);
            var letterMat = new THREE.MeshPhysicalMaterial({
                color: 0xffffff,
                roughness: 0.15,
                metalness: 0.0,
                clearcoat: 0.4,
            });
            var letterMesh = new THREE.Mesh(letterGeo, letterMat);
            letterMesh.position.set(0, bh * 0.55, 0);
            letterMesh.rotation.x = 0.08;
            letterMesh.castShadow = true;
            envGroup.add(letterMesh);

            // Letter lines (decorative)
            var lineMat = new THREE.MeshBasicMaterial({ color: 0xc4b5fd, transparent: true, opacity: 0.5 });
            for (var li = 0; li < 4; li++) {
                var lineGeo = new THREE.BoxGeometry(bw * 0.52, 0.035, 0.025);
                var lineMesh = new THREE.Mesh(lineGeo, lineMat);
                lineMesh.position.set(0, bh * 0.58 + li * 0.12, 0.02);
                envGroup.add(lineMesh);
            }

            // --- Seal / Wax stamp ---
            var sealGeo = new THREE.CylinderGeometry(0.18, 0.2, 0.06, 32);
            var sealMesh = new THREE.Mesh(sealGeo, sealMat);
            sealMesh.position.set(0, bh * 0.02, bh / 2 + 0.04);
            sealMesh.castShadow = true;
            envGroup.add(sealMesh);

            // Seal ring
            var ringGeo = new THREE.TorusGeometry(0.14, 0.025, 16, 32);
            var ringMesh = new THREE.Mesh(ringGeo, sealMat.clone());
            ringMesh.material.emissiveIntensity = 0.3;
            ringMesh.position.set(0, bh * 0.02, bh / 2 + 0.08);
            ringMesh.rotation.x = Math.PI / 2;
            envGroup.add(ringMesh);

            // --- Shadow plane ---
            var shadowGeo = new THREE.PlaneGeometry(4, 4);
            var shadowPlane = new THREE.Mesh(shadowGeo, shadowMat);
            shadowPlane.rotation.x = -Math.PI / 2;
            shadowPlane.position.y = -0.08;
            envGroup.add(shadowPlane);

            envGroup.position.y = -0.3;
            scene.add(envGroup);

            /* --- Floating particles --- */
            var particleCount = 40;
            var particleGeo = new THREE.BufferGeometry();
            var positions = new Float32Array(particleCount * 3);
            var colors = new Float32Array(particleCount * 3);
            var pColors = [
                [0.427, 0.404, 0.894],
                [1, 0.42, 0.796],
                [0.212, 0.945, 0.804],
                [0.788, 0.71, 0.984],
            ];
            for (var i = 0; i < particleCount; i++) {
                positions[i * 3]     = (Math.random() - 0.5) * 10;
                positions[i * 3 + 1] = (Math.random() - 0.5) * 8;
                positions[i * 3 + 2] = (Math.random() - 0.5) * 6;
                var pc = pColors[Math.floor(Math.random() * pColors.length)];
                colors[i * 3]     = pc[0];
                colors[i * 3 + 1] = pc[1];
                colors[i * 3 + 2] = pc[2];
            }
            particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            particleGeo.setAttribute('color', new THREE.BufferAttribute(colors, 3));
            var particleMat = new THREE.PointsMaterial({
                size: 0.06,
                vertexColors: true,
                transparent: true,
                opacity: 0.7,
                blending: THREE.AdditiveBlending,
                depthWrite: false,
            });
            var particles = new THREE.Points(particleGeo, particleMat);
            scene.add(particles);

            /* --- Mouse interaction --- */
            var mouseX = 0, mouseY = 0;
            var targetRotX = 0, targetRotY = 0;
            container.addEventListener('mousemove', function (e) {
                var rect = container.getBoundingClientRect();
                mouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
                mouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
            });
            container.addEventListener('mouseleave', function () {
                mouseX = 0; mouseY = 0;
            });

            /* --- Animation --- */
            var clock = new THREE.Clock();
            function animate() {
                requestAnimationFrame(animate);
                var t = clock.getElapsedTime();

                // Envelope float + gentle rotation
                targetRotY = mouseX * 0.5 + Math.sin(t * 0.4) * 0.15;
                targetRotX = mouseY * -0.3 + Math.cos(t * 0.35) * 0.08;

                envGroup.rotation.y += (targetRotY - envGroup.rotation.y) * 0.04;
                envGroup.rotation.x += (targetRotX - envGroup.rotation.x) * 0.04;
                envGroup.position.y = -0.3 + Math.sin(t * 0.6) * 0.12;

                // Flap flap
                flapMesh.rotation.x = -0.35 + Math.sin(t * 1.2) * 0.08;

                // Seal glow pulse
                sealMat.emissiveIntensity = 0.15 + Math.sin(t * 1.8) * 0.1;

                // Letter slide up/down gently
                letterMesh.position.y = bh * 0.55 + Math.sin(t * 0.8) * 0.04;

                // Particles drift
                var pos = particles.geometry.attributes.position.array;
                for (var j = 0; j < particleCount; j++) {
                    pos[j * 3 + 1] += Math.sin(t + j) * 0.001;
                    pos[j * 3]     += Math.cos(t * 0.5 + j) * 0.0008;
                }
                particles.geometry.attributes.position.needsUpdate = true;
                particles.rotation.y = t * 0.02;

                // Lights orbit slightly
                pointLight1.position.x = Math.sin(t * 0.3) * 4;
                pointLight2.position.z = 3 + Math.cos(t * 0.25) * 2;

                renderer.render(scene, camera);
            }
            animate();

            /* --- Resize --- */
            window.addEventListener('resize', function () {
                var w = container.clientWidth || 260;
                var h = container.clientHeight || 240;
                camera.aspect = w / h;
                camera.updateProjectionMatrix();
                renderer.setSize(w, h);
            });
        })();

        /* ========== COUNT UP ========== */
        function animateCount(el) {
            var target = parseFloat(el.dataset.target);
            var duration = 900;
            var decimals = el.dataset.decimals ? parseInt(el.dataset.decimals) : 0;
            var start = performance.now();
            function tick(now) {
                var progress = Math.min((now - start) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                var value = target * eased;
                el.textContent = value.toLocaleString('id-ID', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals,
                });
                if (progress < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        }
        document.querySelectorAll('.count-up').forEach(function (el) {
            new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) { animateCount(entry.target); obs.disconnect(); }
                });
            }, { threshold: 0.4 }).observe(el);
        });

        /* ========== BREAKDOWN BAR ANIMATION ========== */
        document.querySelectorAll('.breakdown-bar-fill').forEach(function (bar) {
            var w = bar.dataset.width;
            bar.style.width = '0%';
            new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        setTimeout(function () { bar.style.width = w + '%'; }, 300);
                        obs.disconnect();
                    }
                });
            }, { threshold: 0.3 }).observe(bar);
        });

        /* ========== WEEKLY TREND ========== */
        var weeklyCategories = @json($weekLabels);
        new ApexCharts(document.querySelector('#weeklyChart'), {
            chart: { type: 'area', height: 320, toolbar: { show: false }, parentHeightOffset: 0, animations: { enabled: true, easing: 'easeinout', speed: 900 } },
            series: [
                { name: @json(__('dashboard.incoming_letter')), data: @json($incomingPerDay) },
                { name: @json(__('dashboard.outgoing_letter')), data: @json($outgoingPerDay) },
                { name: @json(__('dashboard.disposition_letter')), data: @json($dispositionPerDay) },
            ],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#6d67e4', '#ff6bcb', '#36f1cd'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.02, stops: [0, 90, 100] } },
            grid: { borderColor: '#f0f0f5', strokeDashArray: 5 },
            xaxis: { categories: weeklyCategories, labels: { style: { colors: '#8b8ba6' } } },
            yaxis: { labels: { style: { colors: '#8b8ba6' } } },
            legend: { position: 'top', horizontalAlign: 'right' },
            tooltip: { y: { formatter: function (v) { return v + ' ' + @json(__('dashboard.letters')); } } },
        }).render();

        /* ========== TODAY DONUT ========== */
        new ApexCharts(document.querySelector('#todayDonut'), {
            chart: { type: 'donut', height: 300, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
            series: [@json($todayIncomingLetter), @json($todayOutgoingLetter), @json($todayDispositionLetter)],
            labels: [
                @json(__('dashboard.incoming_letter')),
                @json(__('dashboard.outgoing_letter')),
                @json(__('dashboard.disposition_letter')),
            ],
            colors: ['#6d67e4', '#ff6b9d', '#36c2f1'],
            stroke: { width: 0 },
            dataLabels: { enabled: true, formatter: function (v) { return Math.round(v) + '%'; } },
            legend: { position: 'bottom' },
            tooltip: { y: { formatter: function (v) { return v + ' ' + @json(__('dashboard.letters')); } } },
            plotOptions: { pie: { donut: { size: '72%', labels: { show: true, value: { fontSize: '26px', fontWeight: 800 }, total: { show: true, label: @json(__('dashboard.today')), fontSize: '14px' } } } } },
        }).render();

        /* ========== AGENDA CALENDAR ========== */
        (function initAgendaCalendar () {
            var el = document.getElementById('agenda-calendar-admin');
            if (!el || typeof FullCalendar === 'undefined') return;

            var calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                locale: '{{ app()->getLocale() === 'id' ? 'id' : 'en-gb' }}',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                height: 'auto',
                fixedWeekCount: false,
                expandRows: true,
                eventDisplay: 'block',
                nowIndicator: true,
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
                events: '{{ route("agenda-pimpinan.data") }}',
                eventDidMount: function (info) {
                    var loc = info.event.extendedProps && info.event.extendedProps.location;
                    info.el.setAttribute('title', info.event.title + (loc ? ' • ' + loc : ''));
                },
                eventClick: function (info) {
                    if (info.event.extendedProps && info.event.extendedProps.url) {
                        window.location.href = info.event.extendedProps.url;
                    }
                }
            });

            calendar.render();
        })();

    })();
    </script>
@endpush

@section('content')
    @php
        $todayTotal = $todayIncomingLetter + $todayOutgoingLetter + $todayDispositionLetter;
        $pctIn  = $todayTotal > 0 ? round($todayIncomingLetter / $todayTotal * 100) : 0;
        $pctOut = $todayTotal > 0 ? round($todayOutgoingLetter / $todayTotal * 100) : 0;
        $pctDi  = $todayTotal > 0 ? round($todayDispositionLetter / $todayTotal * 100) : 0;
    @endphp
    <div class="row gy-4">

        {{-- ═══════════════ HERO with THREE.JS 3D Envelope ═══════════════ --}}
        <div class="col-12">
            <div class="dash-hero card-body py-4 px-4">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                                <i class="bx bxs-calendar me-1"></i>{{ $currentDate }}
                            </span>
                        </div>
                        <h4 class="mb-1 fw-bold" style="font-size: 1.7rem;">{{ $greeting }}, {{ auth()->user()->name }}</h4>
                        <p class="mb-0 opacity-75">{{ __('dashboard.today_report') }}</p>
                        <div class="d-flex gap-4 mt-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle" style="width:10px;height:10px;background:#36f1a5;"></span>
                                <small class="opacity-75">{{ __('dashboard.month_total') }}: <strong class="text-white">{{ $totalLettersThisMonth }}</strong></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle" style="width:10px;height:10px;background:#ff6bcb;"></span>
                                <small class="opacity-75">{{ __('dashboard.year_total') }}: <strong class="text-white">{{ $totalLettersThisYear }}</strong></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                        <div id="three-envelope"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ STAT CARDS ═══════════════ --}}
        <div class="col-lg col-md-6 col-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="fw-semibold text-muted">{{ __('dashboard.incoming_letter') }}</span>
                            <div class="count-up" data-target="{{ $todayIncomingLetter }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#6d67e4,#5448d6);"><i class="bx bx-envelope-open"></i></div>
                    </div>
                    @if($percentageIncomingLetter > 0)
                        <small class="trend-up"><i class="bx bx-chevron-up"></i>{{ $percentageIncomingLetter }}%</small>
                    @elseif($percentageIncomingLetter < 0)
                        <small class="trend-down"><i class="bx bx-chevron-down"></i>{{ $percentageIncomingLetter }}%</small>
                    @else
                        <small class="text-muted">{{ __('dashboard.no_change') }}</small>
                    @endif
                    <small class="text-muted ms-1">vs {{ __('dashboard.yesterday') }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg col-md-6 col-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="fw-semibold text-muted">{{ __('dashboard.outgoing_letter') }}</span>
                            <div class="count-up" data-target="{{ $todayOutgoingLetter }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#ff6b9d,#e83e8c);"><i class="bx bx-send"></i></div>
                    </div>
                    @if($percentageOutgoingLetter > 0)
                        <small class="trend-up"><i class="bx bx-chevron-up"></i>{{ $percentageOutgoingLetter }}%</small>
                    @elseif($percentageOutgoingLetter < 0)
                        <small class="trend-down"><i class="bx bx-chevron-down"></i>{{ $percentageOutgoingLetter }}%</small>
                    @else
                        <small class="text-muted">{{ __('dashboard.no_change') }}</small>
                    @endif
                    <small class="text-muted ms-1">vs {{ __('dashboard.yesterday') }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg col-md-6 col-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="fw-semibold text-muted">{{ __('dashboard.disposition_letter') }}</span>
                            <div class="count-up" data-target="{{ $todayDispositionLetter }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#36c2f1,#0288d1);"><i class="bx bx-merge"></i></div>
                    </div>
                    @if($percentageDispositionLetter > 0)
                        <small class="trend-up"><i class="bx bx-chevron-up"></i>{{ $percentageDispositionLetter }}%</small>
                    @elseif($percentageDispositionLetter < 0)
                        <small class="trend-down"><i class="bx bx-chevron-down"></i>{{ $percentageDispositionLetter }}%</small>
                    @else
                        <small class="text-muted">{{ __('dashboard.no_change') }}</small>
                    @endif
                    <small class="text-muted ms-1">vs {{ __('dashboard.yesterday') }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg col-md-6 col-6">
            <a href="{{ route('delegation.monitoring') }}" class="card stat-card h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="fw-semibold text-muted">{{ __('dashboard.active_delegation') }}</span>
                            <div class="count-up" data-target="{{ $delegationStats->active }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#36f1a5,#00b874);"><i class="bx bx-share-alt"></i></div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <small class="trend-up"><i class="bx bx-task me-1"></i>{{ $delegationStats->done }} {{ __('dashboard.done_task') }}</small>
                        @if($delegationStats->taskLate > 0)
                            <small class="trend-down"><i class="bx bx-time me-1"></i>{{ $delegationStats->taskLate }} {{ __('dashboard.late_task') }}</small>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg col-md-6 col-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="fw-semibold text-muted">{{ __('dashboard.active_user') }}</span>
                            <div class="count-up" data-target="{{ $activeUser }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#fbbf24,#f59e0b);"><i class="bx bx-user-check"></i></div>
                    </div>
                    <small class="text-muted">{{ __('dashboard.online_now') }}</small>
                </div>
            </div>
        </div>

        {{-- ═══════════════ TODAY BREAKDOWN (Modern UI) ═══════════════ --}}
        <div class="col-lg-6 col-md-6">
            <div class="card breakdown-card h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">{{ __('dashboard.today_breakdown') }}</h5>
                    <span class="badge bg-label-primary rounded-pill">{{ __('dashboard.today') }}</span>
                </div>
                <div class="card-body">
                    {{-- Incoming --}}
                    <div class="breakdown-row">
                        <div class="breakdown-icon" style="background:linear-gradient(135deg,#6d67e4,#5448d6);"><i class="bx bx-envelope-open"></i></div>
                        <div class="breakdown-info">
                            <div class="label">{{ __('dashboard.incoming_letter') }}</div>
                            <div class="value" style="color:#6d67e4;">{{ $todayIncomingLetter }}</div>
                            <div class="breakdown-bar"><div class="breakdown-bar-fill" data-width="{{ $pctIn }}" style="background:linear-gradient(90deg,#6d67e4,#8b83f7);"></div></div>
                        </div>
                        <div class="breakdown-pct" style="background:rgba(109,103,228,0.1);color:#5448d6;">{{ $pctIn }}%</div>
                    </div>
                    {{-- Outgoing --}}
                    <div class="breakdown-row">
                        <div class="breakdown-icon" style="background:linear-gradient(135deg,#ff6b9d,#e83e8c);"><i class="bx bx-send"></i></div>
                        <div class="breakdown-info">
                            <div class="label">{{ __('dashboard.outgoing_letter') }}</div>
                            <div class="value" style="color:#e83e8c;">{{ $todayOutgoingLetter }}</div>
                            <div class="breakdown-bar"><div class="breakdown-bar-fill" data-width="{{ $pctOut }}" style="background:linear-gradient(90deg,#ff6b9d,#f472b6);"></div></div>
                        </div>
                        <div class="breakdown-pct" style="background:rgba(255,107,157,0.1);color:#e83e8c;">{{ $pctOut }}%</div>
                    </div>
                    {{-- Disposition --}}
                    <div class="breakdown-row">
                        <div class="breakdown-icon" style="background:linear-gradient(135deg,#22d3ee,#0288d1);"><i class="bx bx-merge"></i></div>
                        <div class="breakdown-info">
                            <div class="label">{{ __('dashboard.disposition_letter') }}</div>
                            <div class="value" style="color:#0288d1;">{{ $todayDispositionLetter }}</div>
                            <div class="breakdown-bar"><div class="breakdown-bar-fill" data-width="{{ $pctDi }}" style="background:linear-gradient(90deg,#22d3ee,#38bdf8);"></div></div>
                        </div>
                        <div class="breakdown-pct" style="background:rgba(34,211,238,0.1);color:#0288d1;">{{ $pctDi }}%</div>
                    </div>
                    {{-- Total --}}
                    <div class="breakdown-total-row">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-bar-chart-alt-2" style="font-size:1.3rem;color:var(--surat-primary);"></i>
                            <span class="fw-bold" style="font-size:0.9rem;">{{ __('dashboard.letter_transaction') }}</span>
                        </div>
                        <span class="total-value">{{ $todayTotal }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ QUICK ACTIONS ═══════════════ --}}
        <div class="col-lg-6 col-md-6">
            <div class="card quick-action-card h-100">
                <div class="card-header py-3">
                    <h5 class="mb-0 fw-bold">{{ __('dashboard.quick_actions') }}</h5>
                </div>
                <div class="card-body d-grid" style="grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                    <a href="{{ route('transaction.incoming.create') }}" class="quick-action-tile">
                        <div class="quick-action-icon" style="background:linear-gradient(135deg,#6d67e4,#5448d6);"><i class="bx bx-plus-circle"></i></div>
                        <span>{{ __('dashboard.qa_new_incoming') }}</span>
                    </a>
                    <a href="{{ route('transaction.outgoing.create') }}" class="quick-action-tile">
                        <div class="quick-action-icon" style="background:linear-gradient(135deg,#ff6b9d,#e83e8c);"><i class="bx bx-send"></i></div>
                        <span>{{ __('dashboard.qa_new_outgoing') }}</span>
                    </a>
                    <a href="{{ route('transaction.incoming.index') }}" class="quick-action-tile">
                        <div class="quick-action-icon" style="background:linear-gradient(135deg,#22d3ee,#0891b2);"><i class="bx bx-search"></i></div>
                        <span>{{ __('dashboard.qa_search_incoming') }}</span>
                    </a>
                    <a href="{{ route('archive.index') }}" class="quick-action-tile">
                        <div class="quick-action-icon" style="background:linear-gradient(135deg,#fbbf24,#f59e0b);"><i class="bx bx-archive-in"></i></div>
                        <span>{{ __('dashboard.qa_archive') }}</span>
                    </a>
                    <a href="{{ route('transaction.outgoing.index') }}" class="quick-action-tile">
                        <div class="quick-action-icon" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);"><i class="bx bx-folder-open"></i></div>
                        <span>{{ __('dashboard.qa_view_outgoing') }}</span>
                    </a>
                    <a href="{{ route('transaction.incoming.index') }}" class="quick-action-tile">
                        <div class="quick-action-icon" style="background:linear-gradient(135deg,#0ea5e9,#0369a1);"><i class="bx bx-merge"></i></div>
                        <span>{{ __('dashboard.qa_disposition') }}</span>
                    </a>
                    <a href="{{ route('delegation.index') }}" class="quick-action-tile">
                        <div class="quick-action-icon" style="background:linear-gradient(135deg,#10b981,#047857);"><i class="bx bx-task"></i></div>
                        <span>{{ __('dashboard.qa_delegation') }}</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══════════════ E-DELEGASI OVERVIEW (ADMIN) ═══════════════ --}}
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-share-alt me-2 text-primary"></i>{{ __('dashboard.delegation_overview') }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('delegation.monitoring') }}" class="btn btn-sm btn-outline-primary">{{ __('dashboard.view_all') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-6">
                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-between border rounded-3 p-3">
                                <div><small class="text-muted d-block">{{ __('dashboard.active_delegation') }}</small><strong class="fs-4">{{ $delegationStats->active }}</strong></div>
                                <i class="bx bx-task text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-between border rounded-3 p-3">
                                <div><small class="text-muted d-block">{{ __('dashboard.late_task') }}</small><strong class="fs-4 text-danger">{{ $delegationStats->taskLate }}</strong></div>
                                <i class="bx bx-time text-danger fs-3"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-between border rounded-3 p-3">
                                <div><small class="text-muted d-block">{{ __('dashboard.done_task') }}</small><strong class="fs-4 text-success">{{ $delegationStats->done }}</strong></div>
                                <i class="bx bx-check-double text-success fs-3"></i>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-between border rounded-3 p-3">
                                <div><small class="text-muted d-block">{{ __('dashboard.today_agenda') }}</small><strong class="fs-4 text-warning">{{ $delegationStats->todayAgenda }}</strong></div>
                                <i class="bx bx-calendar-star text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>{{ __('delegation.delegation') }}</th>
                                    <th>{{ __('delegation.staff') }}</th>
                                    <th>{{ __('delegation.priority') }}</th>
                                    <th>{{ __('delegation.deadline') }}</th>
                                    <th>{{ __('delegation.progress') }}</th>
                                    <th>{{ __('delegation.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDelegationRows as $delegation)
                                    <tr>
                                        <td>
                                            <a href="{{ route('delegation.show', $delegation->id) }}" class="fw-semibold text-decoration-none">{{ $delegation->title }}</a>
                                            @if($delegation->letter)
                                                <div class="small text-muted">{{ $delegation->letter->reference_number }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($delegation->tasks as $task)
                                                <span class="badge bg-label-primary me-1">{{ $task->staff?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td><span class="badge {{ \App\Enums\Priority::badge($delegation->priority) }}">{{ $delegation->priority_label }}</span></td>
                                        <td class="small">{{ $delegation->formatted_deadline ?? '-' }}</td>
                                        <td style="min-width:130px;">
                                            @php $avg = $delegation->tasks->where('status', '<>', 'ditolak')->avg('progress'); @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ round($avg ?? 0) }}%"></div>
                                                </div>
                                                <small class="fw-bold">{{ round($avg ?? 0) }}%</small>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-primary">{{ $delegation->status_label }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">{{ __('delegation.empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ ACTIVITY TIMELINE ═══════════════ --}}
        <div class="col-12">
            <div class="card timeline-card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">{{ __('dashboard.recent_activity') }}</h5>
                    <span class="badge bg-label-primary rounded-pill">{{ __('dashboard.latest') }}</span>
                </div>
                <div class="card-body" style="max-height: 360px; overflow-y: auto;">
                    @forelse($recentActivities as $act)
                        <div class="tl-item">
                            <div class="tl-dot" style="background:linear-gradient(135deg, var(--surat-primary), var(--surat-primary-dark));">
                                <i class="bx {{ $act['icon'] }}"></i>
                            </div>
                            <div class="tl-content">
                                <h6>{{ $act['title'] }}</h6>
                                <p>{{ $act['subtitle'] }}</p>
                            </div>
                            <span class="tl-time">{{ $act['time'] }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bx bx-time-five" style="font-size:2.5rem;"></i>
                            <p class="mt-2 mb-0">{{ __('dashboard.no_activity') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══════════════ KALENDER AGENDA PIMPINAN ═══════════════ --}}
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-calendar me-2 text-primary"></i>{{ __('dashboard.agenda_calendar') }}</h5>
                    <a href="{{ route('agenda-pimpinan.index') }}" class="btn btn-sm btn-outline-primary">{{ __('dashboard.view_calendar') }}</a>
                </div>
                <div class="card-body">
                    <div id="agenda-calendar-admin"></div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ CHARTS ═══════════════ --}}
        <div class="col-lg-8">
            <div class="card chart-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">{{ __('dashboard.weekly_trend') }}</h5>
                    <span class="badge bg-label-primary rounded-pill">{{ __('dashboard.last_7_days') }}</span>
                </div>
                <div class="card-body">
                    <div id="weeklyChart"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card chart-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">{{ __('dashboard.today_graphic') }}</h5>
                    <span class="badge bg-label-warning rounded-pill">{{ __('dashboard.today') }}</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="todayDonut"></div>
                </div>
            </div>
        </div>

    </div>
@endsection
