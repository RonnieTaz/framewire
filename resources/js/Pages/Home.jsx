import BaseLayout from "../Layouts/Base";
import {Head, Link} from "@inertiajs/react";

const Home = () => (
    <>
        <Head title="Welcome To Framewire"/>
        <section className="max-w-7xl mx-auto py-48">
            <div className="flex items-center space-y-6 flex-col justify-center ">
                <h1 className="text-7xl font-bold">Welcome to Framewire</h1>
                <p className="text-2xl text-center max-w-4xl leading-8">🚀 Introducing FrameWire—an educational dive into
                    PHP's core & framework internals! 🧠 Perfect for eager learners, students, and curious minds wanting
                    to unravel the magic behind frameworks. Join our exploration!<br/> ❗ Note: Not for production use.
                </p>
                <div className="flex max-w-xl space-x-6 justify-between items-center">
                    <Link href="#" className="rounded-lg font-semibold px-4 py-2 bg-gray-900 text-white">Learn
                        More</Link>
                </div>
            </div>
        </section>
        <section className="py-36 bg-white">
            <div className="max-w-7xl mx-auto grid grid-cols-3 place-content-center gap-x-8">
                <div className="flex flex-col space-y-4">
                    <span className="bg-gray-100 w-fit rounded-lg px-2 py-1 text-sm">Educational Journey</span>
                    <div>
                        <h2 className="text-4xl font-bold leading-8 mb-3">
                            Dive Deep into PHP's Core
                        </h2>
                        <p className="text-gray-600">
                            FrameWire provides an educational journey into PHP's core and framework internals. Perfect
                            for eager learners, students, and curious minds.
                        </p>
                    </div>
                </div>
                <div className="flex flex-col space-y-4">
                    <span className="bg-gray-100 w-fit rounded-lg px-2 py-1 text-sm">Not for Production</span>
                    <div>
                        <h2 className="text-4xl font-bold leading-8 mb-3">
                            A Learning Tool, Not a Production Framework
                        </h2>
                        <p className="text-gray-600">
                            FrameWire is designed for educational purposes and is not intended for production use. It's
                            a tool for understanding, not building.
                        </p>
                    </div>
                </div>
                <div className="flex flex-col space-y-4">
                    <span className="bg-gray-100 w-fit rounded-lg px-2 py-1 text-sm">Join the Exploration</span>
                    <div>
                        <h2 className="text-4xl font-bold leading-8 mb-3">
                            Be Part of Our Learning Community
                        </h2>
                        <p className="text-gray-600">
                            FrameWire is a journey of exploration and learning. Join us and be part of our vibrant
                            learning community.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </>
);

Home.layout = page => <BaseLayout children={page}/>

export default Home;
