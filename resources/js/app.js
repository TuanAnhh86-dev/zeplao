import './bootstrap';
import QRCode from 'qrcode';
import { Html5Qrcode } from 'html5-qrcode';

window.QRCode = QRCode;
window.Html5Qrcode = Html5Qrcode;
window.dispatchEvent(new Event('tixtak:qr-scanner-ready'));
