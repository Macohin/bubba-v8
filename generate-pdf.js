const puppeteer = require('puppeteer');
const fs = require('fs').promises;
const path = require('path');

/**
 * Generates a PDF from a local HTML file and writes it to stdout.
 * @param {string} htmlFilePath - The absolute path to the HTML file to convert.
 */
async function generatePdf(htmlFilePath) {
    let browser = null;
    try {
        // Launch Puppeteer.
        // The --no-sandbox and --disable-setuid-sandbox flags are often required
        // when running in a containerized or restricted server environment.
        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();

        // Go to the local HTML file.
        // It's crucial to use `file://` protocol for local files.
        await page.goto(`file://${htmlFilePath}`, {
            waitUntil: 'networkidle0' // Wait until all network connections are idle
        });

        // Generate the PDF based on the page's content and CSS.
        const pdfBuffer = await page.pdf({
            format: 'A4',
            printBackground: true,      // Ensures background colors and images are printed
            preferCSSPageSize: true,    // Respects the @page size defined in the CSS
            margin: { // Redundant if @page margin is set, but good as a fallback
                top: '20mm',
                right: '20mm',
                bottom: '20mm',
                left: '20mm'
            }
        });

        // Write the generated PDF buffer to standard output.
        // The calling PHP script will capture this output.
        process.stdout.write(pdfBuffer);

    } catch (error) {
        // If an error occurs, write it to standard error so the PHP script can capture it.
        console.error('Puppeteer PDF Generation Error:', error);
        process.exit(1); // Exit with a non-zero code to indicate failure
    } finally {
        // Ensure the browser is always closed.
        if (browser) {
            await browser.close();
        }
    }
}

// --- Script Execution ---
// Get the HTML file path from the command-line arguments.
const filePath = process.argv[2];

if (!filePath) {
    console.error('Error: No HTML file path provided.');
    console.error('Usage: node generate-pdf.js <path_to_html_file>');
    process.exit(1);
}

// Check if the file exists before attempting to process it.
fs.access(filePath)
    .then(() => {
        generatePdf(filePath);
    })
    .catch(err => {
        console.error(`Error: File not found at ${filePath}`);
        process.exit(1);
    });
