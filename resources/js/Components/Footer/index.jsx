const Footer = () => (
    <footer className="bg-white">
        <div className="flex flex-col max-w-4xl mx-auto items-center space-y-4 py-24">
            <h3 className="font-bold text-6xl">Get Started with Framewire</h3>
            <p className="text-xl text-gray-600">
                Sign up to receive the latest updates, news, and tutorials about Framewire.
            </p>
            <form className="flex-col flex items-center" method="post">
                <div className="flex space-x-4">
                    <input className="rounded-lg border-gray-400 border-[1px] px-4 py-2 min-w-72"
                           placeholder="Enter your Email" type="email"/>
                    <button type="submit" className="rounded-lg bg-gray-900 text-white py-2 px-4 font-semibold">Sign
                        Up
                    </button>
                </div>
                <small className="text-gray-500 mt-1">By signing up, you agree to our <a href="#" className="underline">Terms
                    & Conditions</a></small>
            </form>
        </div>
        <div className="border-t border-gray-300 py-5">
            <div className="flex items-center justify-between max-w-6xl mx-auto text-gray-500 text-sm">
                <p>&copy; {(new Date()).getFullYear()} Framewire. No rights reserved.</p>
                <div className="flex items-center space-x-6 text-gray-800">
                    <a href="#">Terms of service</a>
                    <a href="#">Privacy</a>
                </div>
            </div>
        </div>
    </footer>
)

export default Footer
