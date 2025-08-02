"use client";
import React from "react";
import {HeroUIProvider} from "@heroui/react";

type AppProps = {
    children:React.ReactNode
}
const BlogForge = ({children}:AppProps) => {
    return (
        <>
            <HeroUIProvider>
                {children}
            </HeroUIProvider>
        </>
    )
}
export default BlogForge;
