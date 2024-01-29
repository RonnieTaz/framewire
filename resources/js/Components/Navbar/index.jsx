import { IoCodeOutline } from "react-icons/io5";
import { Link } from '@inertiajs/react'

const Navbar = () => (
    <header className="bg-white px-6 py-6">
        <Link href="/" className="flex space-x-6 items-center w-fit">
            <IoCodeOutline className="text-3xl font-bold" />
            <span className="font-bold text-xl">Framewire</span>
        </Link>
    </header>
)

export default Navbar
