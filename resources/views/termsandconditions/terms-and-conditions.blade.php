<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Terms and Conditions - Dr. Vora's Dental Care</title>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
        }

        .header {
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .logo {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
        }

        .logo svg {
            width: 100%;
            height: 100%;
        }

        .header-text {
            flex: 1;
            margin-top: 20px;
            margin-left: 50px;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .content-box {
            background: white;
            border-radius: 8px;
            padding: 3rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .last-updated {
            background: #e3f2fd;
            border-left: 4px solid #1e88e5;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 4px;
        }

        .last-updated strong {
            color: #1565c0;
        }

        h2 {
            color: #1565c0;
            font-size: 1.5rem;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e3f2fd;
        }

        h2:first-of-type {
            margin-top: 0;
        }

        h3 {
            color: #424242;
            font-size: 1.2rem;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
        }

        p {
            margin-bottom: 1rem;
            text-align: justify;
        }

        ul, ol {
            margin-bottom: 1rem;
            margin-left: 2rem;
        }

        li {
            margin-bottom: 0.5rem;
        }

        .highlight {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            border-radius: 4px;
        }

        .contact-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .contact-info h3 {
            margin-top: 0;
            color: #1565c0;
        }

        .contact-info p {
            margin-bottom: 0.5rem;
        }

        .digital-signature-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            margin: 2rem 0;
        }

        .signature-container {
            max-width: 100%;
        }

        .signature-pad {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .signature-controls {
            text-align: center;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 0.5rem;
            transition: all 0.3s;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-primary {
            background: #1e88e5;
            color: white;
        }

        .btn-primary:hover {
            background: #1565c0;
        }

        .signature-info {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 1.5rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #424242;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1e88e5;
            box-shadow: 0 0 0 2px rgba(30, 136, 229, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container {
                max-width: 100%;
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .header-text {
                margin-top: 1rem;
                margin-left: 0;
            }

            .content-box {
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .signature-controls .btn {
                width: 100%;
                margin: 0.5rem 0;
            }

            #signatureCanvas {
                width: 80%;
                max-width: 350px;
                height: 250px !important;
            }
            
            .digital-signature-section {
                visibility: visible !important;
                padding: 1rem !important;
                margin: 1rem 0 !important;
            }
            
            .digital-signature-section h2 {
                font-size: 1.2rem !important;
                margin-bottom: 1rem !important;
            }
            
            .digital-signature-section p {
                font-size: 0.9rem !important;
                margin-bottom: 1rem !important;
            }
            
            .signature-container {
                padding: 0 !important;
            }
            
            .terms-acceptance label {
                font-size: 0.9rem !important;
                line-height: 1.4 !important;
                padding: 10px !important;
            }
            
            .terms-acceptance input[type="checkbox"] {
                width: 18px !important;
                height: 18px !important;
            }
        }

        @media (max-width: 576px) {
            .content-box {
                padding: 1.5rem;
            }

            .container {
                padding: 0 1rem;
            }

            .header h1 {
                font-size: 1.4rem;
            }

            .header p {
                font-size: 0.85rem;
            }

            #signatureCanvas {
                width: 100%;
                max-width: 300px;
                height: 200px !important;
            }

            .digital-signature-section {
                padding: 0.75rem !important;
                margin: 0.75rem 0 !important;
            }

            .digital-signature-section h2 {
                font-size: 1.1rem !important;
            }

            .digital-signature-section p {
                font-size: 0.85rem !important;
            }

            .terms-acceptance label {
                font-size: 0.85rem !important;
                padding: 8px !important;
            }

            .terms-acceptance input[type="checkbox"] {
                width: 16px !important;
                height: 16px !important;
            }
        }

        @media (max-width: 480px) {
            .content-box {
                padding: 1rem;
            }

            .header h1 {
                font-size: 1.2rem;
            }

            #signatureCanvas {
                width: 100%;
                max-width: 280px;
                height: 180px !important;
            }

            .digital-signature-section h2 {
                font-size: 1rem !important;
                margin-bottom: 0.75rem !important;
            }

            .digital-signature-section p {
                font-size: 0.8rem !important;
                margin-bottom: 0.75rem !important;
            }

            .terms-acceptance label {
                font-size: 0.8rem !important;
                line-height: 1.3 !important;
                padding: 6px !important;
            }

            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        .footer {
            background: #263238;
            color: #cfd8dc;
            padding: 2rem 0;
            margin-top: 3rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .back-link {
            display: inline-block;
            background: #1e88e5;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 2rem;
            transition: background 0.3s;
        }

        .back-link:hover {
            background: #1565c0;
        }

        @media (max-width: 768px) {
            .content-box {
                padding: 1.5rem;
            }

            .container {
                padding: 0 1rem;
            }

            .header-content {
                flex-direction: row;
                gap: 1rem;
            }

            .logo {
                width: 50px;
                height: 50px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img src="https://dental.stgserver.co.in/assets/images/logo/logo.png" alt="Logo">
            </div>
            <div class="header-text">
                <h1>Terms and Conditions</h1>
                <p>Dr. Vora's Dental Care - Patient Agreement and Service Terms</p>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="https://dental.stgserver.co.in/dashboard" class="back-link">← Back to Portal</a>

        <div class="content-box">
            <div class="last-updated">
                <strong>Last Updated:</strong> February 4, 2026
            </div>

            <p>Under the Information Technology Act, 2000, electronic records and electronic signatures are legally valid. Courts and medical councils do accept digital consent, provided it is done correctly.</p>

            <h2>Terms and Conditions</h2>
            
            <p>By proceeding with consultation, diagnosis, or treatment, you acknowledge and agree to the following terms:</p>

            <p><strong>Digital consent is fine if all of these are met:</strong></p>

            <p><strong>1. Diagnosis</strong></p>
             <ul>
                <li>Any diagnosis provided is based on the information, symptoms, medical history, and test results available at the time of consultation. Medical conditions may evolve, and diagnoses may change as new information becomes available.</li>
                
            </ul>
            <p><strong>2. Procedure</strong></p>
             <ul>
                <li>The recommended procedure(s) will be explained to you, including the nature and purpose of the treatment. You understand that the outcome of any procedure cannot be guaranteed.</li>
                
            </ul>
            <p><strong>3. Alternatives</strong></p>
            <ul>
                <li>You will be informed of reasonable alternative treatment options, including the option of no treatment, where applicable. You have the right to ask questions and seek clarification before making a decision.</li>
            </ul>
            <p><strong>4. Risks</strong></p>
            <ul>
                <li>All medical procedures and treatments carry certain risks and potential complications. These risks will be explained to you to the extent reasonably possible. You acknowledge that unforeseen risks may occur.</li>
            </ul>
            <p><strong>5. Benefits</strong></p>
            <ul>
                <li>The expected benefits of the proposed treatment will be discussed with you. You understand that individual results may vary and that no assurance has been given regarding specific outcomes.</li>
            </ul>
            <p><strong>6. Costs</strong></p>
            <ul>
                <li>You will be informed of the estimated costs associated with the diagnosis, procedure, and treatment. You acknowledge that additional costs may arise due to unforeseen medical needs, investigations, or complications.</li>
            </ul>

            <h2>2. Doctor Information</h2>

            @if($doctor)
            <div class="contact-info">
                <h3>{{$doctor->ProviderName}}</h3>
                <p><strong>Clinic:</strong> {{$clinic->Name}}</p>
                <p><strong>Address:</strong> {{$clinic->AddressLine1}}, {{$clinic->City}}, {{$clinic->State}} - {{$clinic->ZipCode ?? '400077'}}</p>
                <p><strong>Email:</strong> {{$doctor->Email ?? 'administrator@ecgplus.com'}}</p>
            </div>
            @else
            <div class="contact-info">
               <p><em>Doctor information not available.</em></p>
            </div>
            @endif
            <h2>2. Patient Information</h2>
            @if($patient)
            <div class="contact-info">
                <h3>{{$patient->FirstName}} {{$patient->LastName}}</h3>
                <p><strong>Address:</strong> {{$patient->AddressLine1}}, {{$patient->City}}, {{$patient->State}} - {{$patient->ZipCode}}</p>
                <p><strong>Phone:</strong> {{$patient->MobileNumber ?? $patient->PhoneNumber}}</p>
                <p><strong>Email:</strong> {{$patient->EmailAddress1}}</p>
                <p><strong>Age:</strong> {{$patient->Age}}</p>
                <p><strong>Gender:</strong> {{$patient->Gender}}</p>
                <p><strong>DOB:</strong> {{$patient->DOB ? $patient->DOB->format('d-M-Y') : ''}}</p>
                
            </div>
            @else
            <div class="contact-info">
                <p><em>Patient information not available.</em></p>
            </div>
            @endif

            <div class="terms-acceptance">
                <label style="display: flex; align-items: center; gap: 10px; margin: 20px 0; cursor: pointer;">
                    <input type="checkbox" id="acceptTerms" onchange="toggleSignatureSection()" style="width: 20px; height: 20px;">
                    <span>I have read, understood, and agree to the Terms and Conditions. I want to provide my digital signature.</span>
                </label>
            </div>

            <div class="digital-signature-section" id="signatureSection">
                <h2>Digital Signature</h2>
                <p>By signing below, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
                
                <div class="signature-container">
                     <!-- <div class="signature-info">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="patientName">Full Name:</label>
                                <input type="text" id="patientName" name="patientName" required placeholder="Enter your full name">
                            </div>
                            <div class="form-group">
                                <label for="patientEmail">Email:</label>
                                <input type="email" id="patientEmail" name="patientEmail" required placeholder="Enter your email address">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="signatureDate">Date:</label>
                                <input type="date" id="signatureDate" name="signatureDate" required>
                            </div>
                            <div class="form-group">
                                <label for="patientPhone">Phone Number:</label>
                                <input type="tel" id="patientPhone" name="patientPhone" required placeholder="Enter your phone number">
                            </div>
                        </div>
                    </div> -->
                    <div class="signature-pad">
                        <canvas id="signatureCanvas" width="960" height="200" style="border: 2px solid #ddd; border-radius: 4px; background: white; cursor: crosshair; margin-top:30px;"></canvas>
                    </div>
                    
                    <div class="signature-controls">
                        <button type="button" id="clearSignature" class="btn btn-secondary">Clear Signature</button>
                        <button type="button" id="saveSignature" class="btn btn-primary">Save & Accept Terms</button>
                    </div>
                    
                   
                </div>
            </div>

            
    </div>

    <div class="footer">
        <div class="footer-content">
            <p>&copy; 2026 Dr. Vora's Dental Care. All Rights Reserved.</p>
            <p>Committed to providing exceptional dental care with integrity and compassion.
        </div>
    </div>

    <!-- Toastr JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Remove PatientID query parameter from URL
        document.addEventListener('DOMContentLoaded', function() {
            const url = new URL(window.location);
            const params = new URLSearchParams(url.search);
            
            // Remove PatientID parameter
            params.delete('PatientID');
            
            // Update URL without the parameter
            url.search = params.toString();
            
            // Update browser history without page reload
            if (params.toString()) {
                window.history.replaceState({}, '', url);
            } else {
                // If no parameters left, remove the query string entirely
                window.history.replaceState({}, '', url.pathname);
            }
        });

        // Digital Signature Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Configure Toastr to prevent errors
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "showDuration": "3000"
            };
            
            // Function to toggle signature section
            window.toggleSignatureSection = function() {
                const checkbox = document.getElementById('acceptTerms');
                const signatureSection = document.getElementById('signatureSection');
                
                console.log('Toggle function called');
                console.log('Checkbox checked:', checkbox ? checkbox.checked : 'Checkbox not found');
                console.log('Signature section:', signatureSection ? 'Found' : 'Not found');
                
                if (checkbox && signatureSection) {
                    if (checkbox.checked) {
                        signatureSection.style.display = 'block';
                        console.log('Showing signature section');
                         resizeCanvas();
                    } else {
                        signatureSection.style.display = 'none';
                        console.log('Hiding signature section');
                    }
                } else {
                    console.error('Elements not found');
                }
            };
            
            const canvas = document.getElementById('signatureCanvas');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            // Set canvas size for high DPI displays
            function resizeCanvas() {
                const rect = canvas.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;
                
                // Set actual canvas size
                canvas.width = rect.width * dpr;
                canvas.height = rect.height * dpr;
                
                // Scale context for high DPI displays
                ctx.scale(dpr, dpr);
                
                // Set drawing styles
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#000';
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            // Get mouse/touch position relative to canvas
            function getPosition(e) {
                const rect = canvas.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;
                
                const scaleX = (canvas.width / dpr) / rect.width;
                const scaleY = (canvas.height / dpr) / rect.height;
                
                let clientX, clientY;
                
                if (e.touches && e.touches.length > 0) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                } else if (e.clientX !== undefined) {
                    clientX = e.clientX;
                    clientY = e.clientY;
                } else {
                    return { x: 0, y: 0 };
                }
                
                const x = (clientX - rect.left) * scaleX;
                const y = (clientY - rect.top) * scaleY;
                
                return { x, y };
            }

            // Start drawing
            function startDrawing(e) {
                e.preventDefault();
                isDrawing = true;
                const pos = getPosition(e);
                lastX = pos.x;
                lastY = pos.y;
                
                // Draw a dot for single clicks
                ctx.beginPath();
                ctx.arc(lastX, lastY, 1, 0, 2 * Math.PI);
                ctx.fill();
            }

            // Draw on canvas
            function draw(e) {
                if (!isDrawing) return;
                
                e.preventDefault();
                const pos = getPosition(e);
                
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                
                lastX = pos.x;
                lastY = pos.y;
            }

            // Stop drawing
            function stopDrawing(e) {
                e.preventDefault();
                isDrawing = false;
            }

            // Mouse events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            // Touch events for mobile
            canvas.addEventListener('touchstart', startDrawing);
            canvas.addEventListener('touchmove', draw);
            canvas.addEventListener('touchend', stopDrawing);
            canvas.addEventListener('touchcancel', stopDrawing);

            // Clear signature
            document.getElementById('clearSignature').addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });

            // Save signature
            document.getElementById('saveSignature').addEventListener('click', function() {
                // Check if signature is drawn (simple check - if canvas has any content)
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const hasSignature = imageData.data.some(channel => channel !== 0 && channel !== 255);
                
                if (!hasSignature) {
                    // Create custom notification instead of Toastr
                    const notification = document.createElement('div');
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #f44336;
                        color: white;
                        padding: 15px 20px;
                        border-radius: 5px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        z-index: 9999;
                        font-family: Arial, sans-serif;
                        font-size: 14px;
                        max-width: 300px;
                    `;
                    notification.textContent = 'Please provide your signature before accepting the terms.';
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 3000);
                    return;
                }

                // Get signature as base64
                const signatureData = canvas.toDataURL();
                
                // Create signature object with all required data
                const signatureObject = {
                    PatientID: '@if($patient) {{ $patient->PatientID }} @endif',
                    ProviderID: '@if($doctor) {{ $doctor->ProviderID }} @endif',
                    signatureData: signatureData
                };

                // Here you can send the data to your backend API
                fetch('/digital-signature', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(signatureObject)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const successNotification = document.createElement('div');
                        successNotification.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: #28a745;
                            color: white;
                            padding: 15px 20px;
                            border-radius: 5px;
                            box-shadow: 0 4px 12px rgba(40,167,69,0.15);
                            z-index: 9999;
                            font-family: Arial, sans-serif;
                            font-size: 14px;
                            max-width: 300px;
                        `;
                        successNotification.textContent = 'Thank you! Your signature has been saved and you have accepted the Terms and Conditions.';
                        document.body.appendChild(successNotification);
                        
                        // Add table after signature section
                        const signatureSection = document.getElementById('signatureSection');
                        
                        setTimeout(() => {
                            if (successNotification.parentNode) {
                                successNotification.parentNode.removeChild(successNotification);
                            }
                            // Redirect to dashboard after notification is removed
                            window.location.href = 'https://dental.stgserver.co.in/dashboard';
                        }, 2000);
                        
                        // Optionally, you can redirect or perform other actions
                        // window.location.href = '/dashboard';
                        
                        // Or disable the form after successful submission
                        document.getElementById('saveSignature').disabled = true;
                        document.getElementById('clearSignature').disabled = true;
                        canvas.style.pointerEvents = 'none';
                        
                    } else {
                        // Show error message
                        const errorNotification = document.createElement('div');
                        errorNotification.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: #f44336;
                            color: white;
                            padding: 15px 20px;
                            border-radius: 5px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            z-index: 9999;
                            font-family: Arial, sans-serif;
                            font-size: 14px;
                            max-width: 300px;
                        `;
                        errorNotification.textContent = data.message || 'Failed to save signature';
                        document.body.appendChild(errorNotification);
                        
                        setTimeout(() => {
                            if (errorNotification.parentNode) {
                                errorNotification.parentNode.removeChild(errorNotification);
                            }
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error message
                    const errorNotification = document.createElement('div');
                    errorNotification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #f44336;
                        color: white;
                        padding: 15px 20px;
                        border-radius: 5px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        z-index: 9999;
                        font-family: Arial, sans-serif;
                        font-size: 14px;
                        max-width: 300px;
                    `;
                    errorNotification.textContent = 'Failed to save signature. Please try again.';
                    document.body.appendChild(errorNotification);
                    
                    setTimeout(() => {
                        if (errorNotification.parentNode) {
                            errorNotification.parentNode.removeChild(errorNotification);
                        }
                    }, 2000);
                });
                
                // Or disable the form after submission
                document.getElementById('saveSignature').disabled = true;
                document.getElementById('clearSignature').disabled = true;
                canvas.style.pointerEvents = 'none';
            });
            document.getElementById('signatureSection').style.display = 'none';
        });
    </script>
</body>
</html>
