import React from "react";
import {Card} from "@heroui/card";
import Footer from "@/ui/footer";

export default function InstallationLockedTemplate() {
    return (
        <div className="flex flex-col w-[100%] h-[30em] justify-center content-center items-center">
            <Card className=" flex flex-col w-[100%] md:w-[50%] flex-wrap p-4 items-start wrap-text">
                <div className="flex w-[100%] text-center">
                    <h2>Installation Locked</h2>
                </div>
                <div className="flex  w-[100%] flex-wrap items-start wrap-text flex-col">
                    <p>Please remove <strong>storage/app/install.lock</strong> from the server in order to continue!</p>
                </div>
            </Card>
            <Footer/>
        </div>
    )
}
