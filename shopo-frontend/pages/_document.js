import Document, { Html, Head, Main, NextScript } from "next/document";

class MyDocument extends Document {
    render() {
        return (
            <Html>
                <Head>
                    {process.env.NEXT_PWA_STATUS === 1 || process.env.NEXT_PWA_STATUS === '1'   && (<link rel="manifest" href="/manifest.json" />)}
                    <meta name="theme-color" content="#fff" />
                </Head>
                <body>
                <Main />
                <NextScript />
                </body>
            </Html>
        );
    }
}

export default MyDocument;