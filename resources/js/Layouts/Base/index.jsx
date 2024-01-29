import Navbar from "../../Components/Navbar";
import Footer from "../../Components/Footer";

const BaseLayout = ({ children }) => {
    return (
        <div className="bg-gray-200">
            <Navbar />
            <main>{children}</main>
            <Footer />
        </div>
    )
}

export default BaseLayout;
