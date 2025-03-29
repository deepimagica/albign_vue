import CryptoJS from "crypto-js";

const secretKey = import.meta.env.VITE_SECRET_KEY || "default_secret_123456";
const hashedKey = CryptoJS.enc.Latin1.parse(
    CryptoJS.SHA256(secretKey).toString(CryptoJS.enc.Latin1).substring(0, 16)
);

export const encryptData = (data) => {
    let jsonString = JSON.stringify(data);
    let encrypted = CryptoJS.AES.encrypt(jsonString, hashedKey, {
        mode: CryptoJS.mode.ECB,
        padding: CryptoJS.pad.Pkcs7,
    });

    return CryptoJS.enc.Base64.stringify(encrypted.ciphertext);
};

export const decryptData = (encryptedData) => {
    try {
        const encrypted = {
            ciphertext: CryptoJS.enc.Base64.parse(encryptedData)
        };
        const decrypted = CryptoJS.AES.decrypt(
            encrypted,
            hashedKey,
            { mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7 }
        );
        return JSON.parse(decrypted.toString(CryptoJS.enc.Utf8));
    } catch (error) {
        console.error("Decryption failed:", error);
        throw error;
    }
};