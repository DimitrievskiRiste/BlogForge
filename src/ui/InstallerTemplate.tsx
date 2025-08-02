"use client";
import React, {use} from "react"
import {InstallationLockedProps} from "@/Types/InstallationLockedProps";
import InstallationLockedTemplate from "@/ui/install/InstallationLockedTemplate";
import Navbar from "@/ui/install/Navbar";
import IndexTemplate from "@/ui/install/IndexTemplate";
import Footer from "@/ui/footer";

const InstallerTemplate = ({data}:{data:Promise<InstallationLockedProps>}) => {
    const d = use(data);
    if(d.is_locked) {
        return (
            <>
                <Navbar/>
                <InstallationLockedTemplate/>
            </>
        )
    }
    return (
        <>
            <Navbar/>
            <IndexTemplate/>
            <Footer/>
        </>
    )
}
export default InstallerTemplate;
